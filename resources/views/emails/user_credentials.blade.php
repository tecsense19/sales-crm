<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your CRM Account Details</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 550px; background-color: #ffffff; border-radius: 16px; border: 1px border #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4f46e5; padding: 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.025em;">Welcome to Sales CRM</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px;">
                            <p style="margin: 0 0 16px 0; font-size: 16px; line-height: 1.5; color: #374151;">Hello <strong>{{ $user->name }}</strong>,</p>
                            <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #4b5563;">An administrator has set up a new account for you on the Sales CRM system. Below are your login credentials and details to access the system.</p>
                            
                            <!-- Credentials box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border-radius: 10px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; width: 120px;">Email Address</td>
                                                <td style="padding-bottom: 12px; font-size: 15px; font-weight: 700; color: #111827;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Password</td>
                                                <td style="padding-bottom: 12px; font-size: 15px; font-family: monospace; font-weight: 700; color: #4f46e5;">{{ $password }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">System Role</td>
                                                <td style="font-size: 14px; font-weight: 700; color: #111827; text-transform: capitalize;">{{ $user->role }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginLink }}" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 12px 32px; border-radius: 8px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); transition: background-color 0.2s;">
                                            Sign In to Your Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.5; color: #6b7280; text-align: center;">Or copy and paste this link in your browser:<br>
                                <a href="{{ $loginLink }}" target="_blank" style="color: #4f46e5; text-decoration: underline; font-size: 13px;">{{ $loginLink }}</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 32px;"><hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 0;"></td>
                    </tr>

                    <!-- Footer Warning -->
                    <tr>
                        <td style="padding: 24px 32px 32px 32px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: top; width: 24px; padding-right: 12px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#d97706" style="width: 20px; height: 20px;">
                                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                        </svg>
                                    </td>
                                    <td>
                                        <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #b45309; font-weight: 500;">
                                            <strong>Security Notice:</strong> For security reasons, please change your password immediately on your Profile page after logging in for the first time.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 550px; margin-top: 20px; text-align: center;">
                    <tr>
                        <td style="font-size: 12px; color: #9ca3af;">
                            &copy; {{ date('Y') }} Sales CRM. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
