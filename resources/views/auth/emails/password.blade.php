点击此处重置您的密码: {{ url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}
