import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  // Main/Public Routes
  {
    path: '/',
    name: 'welcome',
    component: () => import('../views/pages/Welcome.vue'),
    meta: { title: 'Welcome' }
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/pages/About.vue'),
    meta: { title: 'About' }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/pages/Dashboard.vue'),
    meta: { title: 'Dashboard' }
  },
  {
    path: '/contacts',
    name: 'contacts',
    component: () => import('../views/pages/Contacts.vue'),
    meta: { title: 'Contacts' }
  },
  {
    path: '/faq',
    name: 'faq',
    component: () => import('../views/pages/Faq.vue'),
    meta: { title: 'FAQ' }
  },
  {
    path: '/pricing',
    name: 'pricing',
    component: () => import('../views/pages/Pricing.vue'),
    meta: { title: 'Pricing' }
  },

  // Authentication Routes
  {
    path: '/auth',
    name: 'auth',
    meta: { title: 'Auth' },
    children: [
      {
        path: 'login',
        name: 'auth-login',
        component: () => import('../views/pages/auth/Login.vue'),
        meta: { title: 'Login' }
      },
      {
        path: 'register',
        name: 'auth-register',
        component: () => import('../views/pages/auth/Register.vue'),
        meta: { title: 'Register' }
      },
      {
        path: 'forgot-password',
        name: 'auth-forgot-password',
        component: () => import('../views/pages/auth/ForgotPassword.vue'),
        meta: { title: 'Forgot Password' }
      },
      {
        path: 'confirm-password',
        name: 'auth-confirm-password',
        component: () => import('../views/pages/auth/ConfirmPassword.vue'),
        meta: { title: 'Confirm Password' }
      },
      {
        path: 'reset-password',
        name: 'auth-reset-password',
        component: () => import('../views/pages/auth/ResetPassword.vue'),
        meta: { title: 'Reset Password' }
      },
      {
        path: 'verify-email',
        name: 'auth-verify-email',
        component: () => import('../views/pages/auth/VerifyEmail.vue'),
        meta: { title: 'Verify Email' }
      },
      {
        path: 'verify-email-returning',
        name: 'auth-verify-email-returning',
        component: () => import('../views/pages/auth/VerifyEmailReturning.vue'),
        meta: { title: 'Verify Email' }
      },
      {
        path: 'two-factor-challenge',
        name: 'auth-two-factor-challenge',
        component: () => import('../views/pages/auth/TwoFactorChallenge.vue'),
        meta: { title: 'Two Factor Challenge' }
      }
    ]
  },

  // Banking Routes
  {
    path: '/banking',
    name: 'banking',
    meta: { title: 'Banking' },
    children: [
      {
        path: 'dashboard',
        name: 'banking-dashboard',
        component: () => import('../views/pages/banking/Dashboard.vue'),
        meta: { title: 'Banking Dashboard' }
      },
      {
        path: 'cards',
        name: 'banking-cards',
        component: () => import('../views/pages/banking/Cards.vue'),
        meta: { title: 'Cards' }
      },
      {
        path: 'wallet',
        name: 'banking-wallet',
        component: () => import('../views/pages/banking/Wallet.vue'),
        meta: { title: 'Wallet' }
      },
      {
        path: 'deposit',
        name: 'banking-deposit',
        component: () => import('../views/pages/banking/Deposit.vue'),
        meta: { title: 'Deposit' }
      },
      {
        path: 'withdraw',
        name: 'banking-withdraw',
        component: () => import('../views/pages/banking/Withdraw.vue'),
        meta: { title: 'Withdraw' }
      },
      {
        path: 'transfer',
        name: 'banking-transfer',
        component: () => import('../views/pages/banking/Transfer.vue'),
        meta: { title: 'Transfer' }
      },
      {
        path: 'transaction/history',
        name: 'banking-transaction-history',
        component: () => import('../views/pages/banking/TransactionHistory.vue'),
        meta: { title: 'Transaction History' }
      },
      {
        path: 'payment',
        name: 'banking-payment',
        component: () => import('../views/pages/banking/Payment.vue'),
        meta: { title: 'Payment' }
      },
      {
        path: 'auth/codes',
        name: 'banking-auth-codes',
        component: () => import('../views/pages/banking/AuthCodes.vue'),
        meta: { title: 'Auth Codes' }
      },
      {
        path: 'card/application',
        name: 'banking-card-application',
        component: () => import('../views/pages/banking/CardApplication.vue'),
        meta: { title: 'Card Application' }
      },
      {
        path: 'kyc',
        name: 'banking-kyc',
        component: () => import('../views/pages/banking/Kyc.vue'),
        meta: { title: 'KYC' }
      },
      {
        path: 'loan',
        name: 'banking-loan',
        component: () => import('../views/pages/banking/Loan.vue'),
        meta: { title: 'Loan' }
      },
      {
        path: 'settings',
        name: 'banking-settings',
        component: () => import('../views/pages/banking/Settings.vue'),
        meta: { title: 'Banking Settings' }
      },
      {
        path: 'set-withdrawal-pin',
        name: 'banking-set-withdrawal-pin',
        component: () => import('../views/pages/banking/SetWithdrawalPin.vue'),
        meta: { title: 'Set Withdrawal PIN' }
      }
    ]
  },

  // Broker Routes
{
  path: '/broker',
  name: 'broker',
  meta: { title: 'Broker' },
  children: [
    {
      path: 'dashboard',
      name: 'broker-dashboard',
      component: () => import('../views/pages/broker/Dashboard.vue'),
      meta: { title: 'Broker Dashboard' }
    },
    { path: 'spot', 
    name: 'broker-spot',
    component: () => import('../views/pages/broker/Spot.vue'), 
    meta: { title: 'Spot Trading' }
    },
    { path: 'futures', 
    name: 'broker-futures', 
    component: () => import('../views/pages/broker/Futures.vue'), 
    meta: { title: 'Futures Trading' } },
    { path: 'forex', name: 'broker-forex', component: () => import('../views/pages/broker/Forex.vue'), meta: { title: 'Forex Trading' } },
    { path: 'markets', name: 'broker-markets', component: () => import('../views/pages/broker/Markets.vue'), meta: { title: 'Markets' } },
    { path: 'product', name: 'broker-product', component: () => import('../views/pages/broker/Product.vue'), meta: { title: 'Product' } },
    { path: 'market-profile', name: 'broker-market-profile', component: () => import('../views/pages/broker/MarketProfile.vue'), meta: { title: 'Market Profile' } },
    { path: 'trade-view', name: 'broker-trade-view', component: () => import('../views/pages/broker/TradeView.vue'), meta: { title: 'Trade View' } },
    { path: 'transaction-history', name: 'broker-transaction-history', component: () => import('../views/pages/broker/TransactionHistory.vue'), meta: { title: 'Transaction History' } },
    { path: 'transfer', name: 'broker-transfer', component: () => import('../views/pages/broker/Transfer.vue'), meta: { title: 'Transfer' } },
    { path: 'wallet', name: 'broker-wallet', component: () => import('../views/pages/broker/Wallet.vue'), meta: { title: 'Wallet' } },
    { path: 'withdraw', name: 'broker-withdraw', component: () => import('../views/pages/broker/Withdraw.vue'), meta: { title: 'Withdraw' } },
    { path: 'auth-codes', name: 'broker-auth-codes', component: () => import('../views/pages/broker/AuthCodes.vue'), meta: { title: 'Auth Codes' } },
    { path: 'kyc', name: 'broker-kyc', component: () => import('../views/pages/broker/Kyc.vue'), meta: { title: 'KYC' } },
    { path: 'copy-trading', name: 'broker-copy-trading', component: () => import('../views/pages/broker/CopyTrading.vue'), meta: { title: 'Copy Trading' } },
    { path: 'copy-dashboard', name: 'broker-copy-dashboard', component: () => import('../views/pages/broker/CopyDashboard.vue'), meta: { title: 'Copy Dashboard' } },
    { path: 'copy-trade-history', name: 'broker-copy-trade-history', component: () => import('../views/pages/broker/CopyTradeHistory.vue'), meta: { title: 'Copy Trade History' } },
    { path: 'copy-settings-sheet', name: 'broker-copy-settings-sheet', component: () => import('../views/pages/broker/CopySettingsSheet.vue'), meta: { title: 'Copy Settings Sheet' } },
    { path: 'copied-trades', name: 'broker-copied-trades', component: () => import('../views/pages/broker/CopiedTrades.vue'), meta: { title: 'Copied Trades' } },
    { path: 'trader-profile', name: 'broker-trader-profile', component: () => import('../views/pages/broker/TraderProfile.vue'), meta: { title: 'Trader Profile' } },
    { path: 'set-withdrawal-pin', name: 'broker-set-withdrawal-pin', component: () => import('../views/pages/broker/SetWithdrawalPin.vue'), meta: { title: 'Set Withdrawal PIN' } },
  ]
},

  // Settings Routes
  {
    path: '/settings',
    name: 'settings',
    meta: { title: 'Settings' },
    children: [
      {
        path: 'profile',
        name: 'settings-profile',
        component: () => import('../views/pages/settings/Profile.vue'),
        meta: { title: 'Profile Settings' }
      },
      {
        path: 'password',
        name: 'settings-password',
        component: () => import('../views/pages/settings/Password.vue'),
        meta: { title: 'Password Settings' }
      },
      {
        path: 'appearance',
        name: 'settings-appearance',
        component: () => import('../views/pages/settings/Appearance.vue'),
        meta: { title: 'Appearance Settings' }
      },
      {
        path: 'two-factor',
        name: 'settings-two-factor',
        component: () => import('../views/pages/settings/TwoFactor.vue'),
        meta: { title: 'Two Factor Settings' }
      }
    ]
  },

  // Admin Routes
  {
    path: '/admin',
    name: 'admin',
    meta: { title: 'Admin' },
    children: [
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('../views/pages/admin/Dashboard.vue'),
        meta: { title: 'Admin Dashboard' }
      },
      {
        path: 'admins',
        name: 'admin-admins',
        component: () => import('../views/pages/admin/Admins.vue'),
        meta: { title: 'Admins' }
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('../views/pages/admin/Users.vue'),
        meta: { title: 'Users' }
      },
      {
        path: 'settings',
        name: 'admin-settings',
        component: () => import('../views/pages/admin/Settings.vue'),
        meta: { title: 'Admin Settings' }
      },
      {
        path: 'layout',
        name: 'admin-layout',
        component: () => import('../views/pages/admin/AdminLayout.vue'),
        meta: { title: 'Admin Layout' }
      },
      // Admin Bank Routes
      {
        path: 'bank/active-users',
        name: 'admin-bank-active-users',
        component: () => import('../views/pages/admin/Bank/ActiveUsers.vue'),
        meta: { title: 'Active Users' }
      },
      {
        path: 'bank/all-users',
        name: 'admin-bank-all-users',
        component: () => import('../views/pages/admin/Bank/AllUsers.vue'),
        meta: { title: 'All Users' }
      },
      {
        path: 'bank/cards',
        name: 'admin-bank-cards',
        component: () => import('../views/pages/admin/Bank/Cards.vue'),
        meta: { title: 'Cards' }
      },
      {
        path: 'bank/credit-user',
        name: 'admin-bank-credit-user',
        component: () => import('../views/pages/admin/Bank/CreditUser.vue'),
        meta: { title: 'Credit User' }
      },
      {
        path: 'bank/kyc',
        name: 'admin-bank-kyc',
        component: () => import('../views/pages/admin/Bank/KYC.vue'),
        meta: { title: 'KYC' }
      },
      {
        path: 'bank/billing-codes',
        name: 'admin-bank-billing-codes',
        component: () => import('../views/pages/admin/Bank/BillingCodes.vue'),
        meta: { title: 'Billing Codes' }
      },
      {
        path: 'bank/withdrawals',
        name: 'admin-bank-withdrawals',
        component: () => import('../views/pages/admin/Bank/Withdrawals.vue'),
        meta: { title: 'Withdrawals' }
      },
      // Admin Broker Routes
      {
        path: 'broker/active-trades',
        name: 'admin-broker-active-trades',
        component: () => import('../views/pages/admin/Broker/ActiveTrades.vue'),
        meta: { title: 'Active Trades' }
      },
      {
        path: 'broker/all-users',
        name: 'admin-broker-all-users',
        component: () => import('../views/pages/admin/Broker/AllUsers.vue'),
        meta: { title: 'All Users' }
      },
      {
        path: 'broker/billing-codes',
        name: 'admin-broker-billing-codes',
        component: () => import('../views/pages/admin/Broker/BillingCodes.vue'),
        meta: { title: 'Billing Codes' }
      },
      {
        path: 'broker/credit-user',
        name: 'admin-broker-credit-user',
        component: () => import('../views/pages/admin/Broker/CreditUser.vue'),
        meta: { title: 'Credit User' }
      },
      {
        path: 'broker/kyc',
        name: 'admin-broker-kyc',
        component: () => import('../views/pages/admin/Broker/KYC.vue'),
        meta: { title: 'KYC' }
      },
      {
        path: 'broker/user-investments',
        name: 'admin-broker-user-investments',
        component: () => import('../views/pages/admin/Broker/UserInvestments.vue'),
        meta: { title: 'User Investments' }
      },
      {
        path: 'broker/withdrawals',
        name: 'admin-broker-withdrawals',
        component: () => import('../views/pages/admin/Broker/Withdrawals.vue'),
        meta: { title: 'Withdrawals' }
      },
      // Admin Users Routes
      {
        path: 'users/active-users',
        name: 'admin-users-active-users',
        component: () => import('../views/pages/admin/Users/ActiveUsers.vue'),
        meta: { title: 'Active Users' }
      },
      {
        path: 'users/all-users',
        name: 'admin-users-all-users',
        component: () => import('../views/pages/admin/Users/AllUsers.vue'),
        meta: { title: 'All Users' }
      },
      {
        path: 'users/billing-codes',
        name: 'admin-users-billing-codes',
        component: () => import('../views/pages/admin/Users/BillingCodes.vue'),
        meta: { title: 'Billing Codes' }
      },
      {
        path: 'users/kyc',
        name: 'admin-users-kyc',
        component: () => import('../views/pages/admin/Users/KYC.vue'),
        meta: { title: 'KYC' }
      },
      {
        path: 'users/withdrawals',
        name: 'admin-users-withdrawals',
        component: () => import('../views/pages/admin/Users/Withdrawals.vue'),
        meta: { title: 'Withdrawals' }
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

// Optional: Add navigation guards
router.beforeEach((to, from, next) => {
  // You can add authentication checks or other logic here
  next();
});

export default router;
