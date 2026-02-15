<!doctype html>
<html>
  <body>
    <p>Hello {{ $transaction->user->name }},</p>
    <p>You have received {{ $transaction->amount }} {{ $transaction->currency }} into your account.</p>
    <p>Description: {{ $transaction->description }}</p>
    <p>Reference: {{ $transaction->id }}</p>
    <p>Thank you,<br/>The Team</p>
  </body>
</html>
