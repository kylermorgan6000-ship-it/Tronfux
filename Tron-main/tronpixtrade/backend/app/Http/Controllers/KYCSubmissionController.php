<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKYCSubmissionRequest;
use App\Http\Requests\RejectKYCRequest;
use App\Models\KYCSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KYCSubmissionController extends ApiController
{
    /**
     * List KYC submissions (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can view KYC submissions');
            }

            $query = KYCSubmission::query();

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $submissions = $query->latest()->paginate($request->per_page ?? 15);

            return $this->successWithPagination($submissions, 'KYC submissions retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve KYC submissions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Submit KYC documents (multipart form)
     */
    public function store(StoreKYCSubmissionRequest $request): JsonResponse
    {
        try {
            // Check if user already has pending or approved KYC
            $existing = KYCSubmission::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existing) {
                return $this->error('You already have a pending or approved KYC submission', 422);
            }

            $documentFrontPath = null;
            $documentBackPath = null;
            $selfiePath = null;

            // Handle file uploads
            if ($request->hasFile('document_front')) {
                $documentFrontPath = $request->file('document_front')->store('kyc/documents', 'public');
            }

            if ($request->hasFile('document_back')) {
                $documentBackPath = $request->file('document_back')->store('kyc/documents', 'public');
            }

            if ($request->hasFile('selfie')) {
                $selfiePath = $request->file('selfie')->store('kyc/selfies', 'public');
            }

            $submission = KYCSubmission::create([
                'user_id' => auth()->id(),
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_front' => $documentFrontPath,
                'document_back' => $documentBackPath,
                'selfie' => $selfiePath,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'status' => 'pending',
            ]);

            return $this->success(['submission' => $submission], 'KYC submission created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create KYC submission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * View KYC submission
     */
    public function show($id): JsonResponse
    {
        try {
            $submission = KYCSubmission::find($id);

            if (!$submission) {
                return $this->notFound('KYC submission not found');
            }

            // Check authorization - own submission or admin
            if ($submission->user_id !== auth()->id() && !auth()->user()->is_admin) {
                return $this->forbidden('You do not have access to this submission');
            }

            return $this->success(['submission' => $submission], 'KYC submission retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve KYC submission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Approve KYC submission (admin only)
     */
    public function approve($id): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can approve KYC submissions');
            }

            $submission = KYCSubmission::find($id);

            if (!$submission) {
                return $this->notFound('KYC submission not found');
            }

            $submission->update(['status' => 'approved']);

            // Update user's KYC verification status
            $submission->user->update(['kyc_verified' => true]);

            return $this->success(['submission' => $submission], 'KYC submission approved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to approve KYC submission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject KYC submission with reason (admin only)
     */
    public function reject(RejectKYCRequest $request, $id): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can reject KYC submissions');
            }

            $submission = KYCSubmission::find($id);

            if (!$submission) {
                return $this->notFound('KYC submission not found');
            }

            $submission->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
            ]);

            return $this->success(['submission' => $submission], 'KYC submission rejected successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to reject KYC submission: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete KYC submission (admin only)
     */
    public function destroy($id): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can delete KYC submissions');
            }

            $submission = KYCSubmission::find($id);

            if (!$submission) {
                return $this->notFound('KYC submission not found');
            }

            // Delete associated files
            $files = ['document_front', 'document_back', 'selfie'];
            foreach ($files as $file) {
                if ($submission->$file && file_exists(storage_path('app/public/' . $submission->$file))) {
                    unlink(storage_path('app/public/' . $submission->$file));
                }
            }

            $submission->delete();

            return $this->success(null, 'KYC submission deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete KYC submission: ' . $e->getMessage(), 500);
        }
    }
}
