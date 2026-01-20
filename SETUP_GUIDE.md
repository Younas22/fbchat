# Facebook Chat Manager - Setup Guide

## Features Implemented

### Core Features
- ✅ **Facebook Page Connection** - Connect multiple Facebook pages from a single account
- ✅ **Real-time Chat** - WebSocket-based real-time messaging using Laravel Reverb
- ✅ **Message Caching** - Messages stored in database for faster loading
- ✅ **Webhook Integration** - Receive instant notifications from Facebook
- ✅ **Unread Message Tracking** - Track unread messages per conversation
- ✅ **Save Chats with Notes** - Save important conversations with custom notes
- ✅ **Search Functionality** - Search conversations by customer name or message content
- ✅ **File/Media Support** - Send and receive images, videos, and files
- ✅ **Encryption** - Facebook access tokens are encrypted in database
- ✅ **Rate Limiting** - API rate limiting to prevent abuse

### Security Features
- 🔐 Access token encryption using Laravel's encryption
- 🔐 API rate limiting on all endpoints
- 🔐 Laravel Sanctum authentication
- 🔐 Proper error handling with exception logging
- 🔐 CSRF protection on web routes

## Installation Steps

### 1. Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL database
- Node.js and NPM
- Facebook Developer Account

### 2. Clone & Install Dependencies

```bash
# Navigate to project directory
cd facebook-chat-manager

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy .env.example to .env
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure .env File

Edit your `.env` file with the following:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=facebook_chat_manager
DB_USERNAME=root
DB_PASSWORD=your_password

# Broadcasting (Laravel Reverb)
BROADCAST_CONNECTION=reverb

# Reverb WebSocket
REVERB_APP_ID=123456
REVERB_APP_KEY=your_random_key
REVERB_APP_SECRET=your_random_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Facebook API
FACEBOOK_APP_ID=your_facebook_app_id
FACEBOOK_APP_SECRET=your_facebook_app_secret
FACEBOOK_GRAPH_API_VERSION=v19.0
FACEBOOK_BUSINESS_ACCOUNT_TOKEN=your_business_token
FACEBOOK_WEBHOOK_VERIFY_TOKEN=your_custom_verify_token
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate
```

### 6. Build Frontend Assets

```bash
# Build for development
npm run dev

# OR build for production
npm run build
```

## Running the Application

### Method 1: Using Laravel Artisan (Development)

Open **3 separate terminal windows**:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```
Access at: http://localhost:8000

**Terminal 2 - Laravel Reverb WebSocket:**
```bash
php artisan reverb:start
```
WebSocket will run on port 8080

**Terminal 3 - Vite Dev Server:**
```bash
npm run dev
```

### Method 2: Using Production Build

```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan reverb:start
```

Then access http://localhost:8000

## Facebook App Configuration

### 1. Create Facebook App

1. Go to https://developers.facebook.com/
2. Create a new app
3. Select "Business" type
4. Add "Messenger" product

### 2. Configure Webhook

In your Facebook App dashboard:

1. Go to Messenger > Settings
2. Add Callback URL: `https://your-domain.com/api/webhook/facebook`
3. Add Verify Token: (same as `FACEBOOK_WEBHOOK_VERIFY_TOKEN` in .env)
4. Subscribe to these webhook events:
   - `messages`
   - `messaging_postbacks`
   - `message_deliveries`
   - `message_reads`

### 3. Get Access Tokens

**Page Access Token:**
1. Go to Graph API Explorer
2. Select your app
3. Select "User Token" → Get Token → Get Page Access Token
4. Select your page
5. Copy the token

**Business Account Token:**
1. Go to Business Settings
2. System Users → Create System User
3. Add Assets → Pages → Select your pages
4. Generate Token with these permissions:
   - `pages_messaging`
   - `pages_read_engagement`
   - `pages_manage_metadata`

### 4. Configure Permissions

Make sure your app has these permissions:
- `pages_messaging`
- `pages_read_engagement`
- `pages_manage_metadata`
- `pages_show_list`

## Usage Guide

### 1. Register/Login

Navigate to the application and create an account.

### 2. Connect Facebook Pages

1. Click "Connect Facebook Pages" button
2. System will fetch all pages associated with your Facebook account
3. Pages will be displayed with their profile pictures

### 3. View Conversations

1. Select a page from the dashboard
2. View all conversations for that page
3. Use search to find specific conversations
4. Click on a conversation to open chat

### 4. Send Messages

1. Open a conversation
2. Type message in input box
3. Press Enter or click Send
4. Messages appear in real-time via WebSocket

### 5. Save Conversations

1. While viewing a conversation, click "Save Chat"
2. Add notes about the conversation
3. Access saved chats from the "Saved Chats" menu

### 6. Search Conversations

1. Go to Conversations page
2. Use search box to find by:
   - Customer name
   - Message content
   - Customer PSID

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `GET /api/me` - Get current user
- `POST /api/logout` - Logout user

### Facebook Pages
- `GET /api/pages` - List all connected pages
- `POST /api/pages/connect` - Connect Facebook pages
- `DELETE /api/pages/{pageId}` - Disconnect page
- `GET /api/pages/{pageId}` - Get page details

### Conversations
- `GET /api/conversations/{pageId}?search=keyword` - List conversations with search
- `POST /api/conversations/{pageId}/sync` - Sync conversations from Facebook
- `PATCH /api/conversations/{conversationId}/archive` - Archive conversation
- `PATCH /api/conversations/{conversationId}/unarchive` - Unarchive conversation

### Chat Messages
- `GET /api/chat/{conversationId}/messages` - Get messages (from database)
- `POST /api/chat/{conversationId}/send` - Send message

### Saved Chats
- `POST /api/saved-chats/{conversationId}` - Save chat with notes
- `GET /api/saved-chats` - List all saved chats
- `PATCH /api/saved-chats/{savedChatId}` - Update notes
- `DELETE /api/saved-chats/{savedChatId}` - Delete saved chat

### Webhook
- `GET /api/webhook/facebook` - Webhook verification
- `POST /api/webhook/facebook` - Webhook handler

## Rate Limits

- **Login/Register:** 10 requests per minute
- **Page Connect:** 5 requests per minute
- **Conversation Sync:** 10 requests per minute
- **Send Message:** 30 requests per minute
- **Other Endpoints:** 60 requests per minute

## Database Schema

### Tables Created

1. **users** - Admin user accounts
2. **facebook_pages** - Connected Facebook pages
3. **conversations** - Chat conversations
4. **messages** - Individual messages (cached)
5. **saved_chats** - Saved conversations with notes

## Troubleshooting

### WebSocket Connection Failed

**Problem:** Real-time messages not working

**Solution:**
1. Make sure Laravel Reverb is running: `php artisan reverb:start`
2. Check REVERB_* variables in .env
3. Check browser console for WebSocket errors

### Facebook Webhook Not Working

**Problem:** Not receiving messages from Facebook

**Solution:**
1. Verify webhook URL is publicly accessible (use ngrok for local testing)
2. Check FACEBOOK_WEBHOOK_VERIFY_TOKEN matches in Facebook app settings
3. Check webhook subscriptions in Facebook app

### Messages Not Loading

**Problem:** Chat shows empty or errors

**Solution:**
1. Run migrations: `php artisan migrate`
2. Check database connection in .env
3. Check Laravel logs: `storage/logs/laravel.log`

### Access Token Errors

**Problem:** "Invalid access token" errors

**Solution:**
1. Generate new page access token
2. Make sure token has required permissions
3. Check token expiration (use long-lived tokens)

## Production Deployment

### 1. Environment Setup

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Use HTTPS for Reverb
REVERB_SCHEME=https
REVERB_PORT=443
```

### 2. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Setup Queue Worker

```bash
# For background jobs
php artisan queue:work --daemon
```

### 4. Setup Supervisor (Linux)

Create `/etc/supervisor/conf.d/laravel-reverb.conf`:

```ini
[program:laravel-reverb]
command=php /path/to/project/artisan reverb:start
directory=/path/to/project
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel-reverb.log
```

### 5. HTTPS Configuration

Make sure to use SSL certificate for:
- Main application
- WebSocket connection (Reverb)
- Webhook URL for Facebook

## Important Notes

1. **Access Token Security:** All Facebook page access tokens are encrypted in the database using Laravel's encryption
2. **Webhook Verification:** The webhook verify token must match between .env and Facebook app settings
3. **Real-time Updates:** Laravel Reverb must be running for real-time message updates
4. **Message Caching:** Messages are cached in database to reduce Facebook API calls
5. **Rate Limiting:** API calls to Facebook are limited to prevent hitting rate limits

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify Facebook webhook logs in Facebook App Dashboard

## License

This is a custom-built application for managing Facebook page messages.
