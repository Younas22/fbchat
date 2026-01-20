<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Facebook Chat Manager</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>

            <div class="space-y-6 text-gray-700">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Information We Collect</h2>
                    <p>We collect information that you provide directly to us when using our Facebook Chat Manager application, including:</p>
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        <li>Facebook Page information and access tokens</li>
                        <li>Conversation and message data from your Facebook Pages</li>
                        <li>Usage data and application logs</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">2. How We Use Your Information</h2>
                    <p>We use the information we collect to:</p>
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        <li>Provide and maintain the Facebook Chat Manager service</li>
                        <li>Display and manage your Facebook Page conversations</li>
                        <li>Send and receive messages on behalf of your Pages</li>
                        <li>Improve and optimize our service</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Data Storage and Security</h2>
                    <p>We implement appropriate security measures to protect your information. Your data is stored securely and is only accessible by authorized personnel.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">4. Data Sharing</h2>
                    <p>We do not sell, trade, or rent your personal information to third parties. We only access your Facebook data as necessary to provide our services.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        <li>Access your personal data</li>
                        <li>Request correction of your data</li>
                        <li>Request deletion of your data</li>
                        <li>Revoke access to your Facebook Pages at any time</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Data Deletion</h2>
                    <p>To request deletion of your data, please visit our <a href="{{ url('/data-deletion') }}" class="text-blue-600 hover:underline">Data Deletion page</a> or contact us directly.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                    <p class="mt-2">
                        <strong>Email:</strong> <a href="mailto:hm.younas22@gmail.com" class="text-blue-600 hover:underline">hm.younas22@gmail.com</a>
                    </p>
                </section>

                <section>
                    <p class="text-sm text-gray-500 mt-8">
                        <strong>Last Updated:</strong> {{ date('F d, Y') }}
                    </p>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
