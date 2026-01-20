<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Deletion Request - Facebook Chat Manager</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Data Deletion Request</h1>

            <div class="space-y-6 text-gray-700">
                <section>
                    <p class="text-lg">We respect your privacy and your right to control your personal data.</p>
                </section>

                <section class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">How to Request Data Deletion</h2>
                    <p class="mb-4">Users can request deletion of their data by contacting us at:</p>
                    <p class="text-lg">
                        <strong>Email:</strong>
                        <a href="mailto:hm.younas22@gmail.com" class="text-blue-600 hover:underline font-semibold">
                            hm.younas22@gmail.com
                        </a>
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">What Data Will Be Deleted?</h2>
                    <p>When you request data deletion, we will remove:</p>
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        <li>Your Facebook Page access tokens</li>
                        <li>Stored conversation and message data</li>
                        <li>Any saved chat records</li>
                        <li>Application usage logs related to your account</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Processing Time</h2>
                    <p>We will process your data deletion request within <strong>30 days</strong> of receiving your email. You will receive a confirmation email once the deletion is complete.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Revoking App Access</h2>
                    <p>You can also revoke our app's access to your Facebook Pages at any time by:</p>
                    <ol class="list-decimal ml-6 mt-2 space-y-1">
                        <li>Going to your Facebook Settings</li>
                        <li>Navigating to "Business Integrations" or "Apps and Websites"</li>
                        <li>Finding "Facebook Chat Manager" and removing it</li>
                    </ol>
                </section>

                <section class="border-t pt-6 mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Questions?</h2>
                    <p>If you have any questions about data deletion or our privacy practices, please contact us at
                        <a href="mailto:hm.younas22@gmail.com" class="text-blue-600 hover:underline">hm.younas22@gmail.com</a>
                    </p>
                    <p class="mt-4">
                        You can also review our full
                        <a href="{{ url('/privacy-policy') }}" class="text-blue-600 hover:underline">Privacy Policy</a>.
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
