# Persistent Image Storage Setup - Cloudinary

## Problem
Railway uses an **ephemeral filesystem** - uploaded images are lost when the container restarts or redeploys. This means you have to re-upload images every time you deploy changes.

## Solution: Cloudinary (Free Tier Available) ✅

Cloudinary provides persistent cloud storage for images. The free tier includes:
- **25 GB storage**
- **25 GB monthly bandwidth**
- **Perfect for small to medium applications**
- **Automatic image optimization**
- **CDN delivery (faster loading)**

## Setup Instructions

### Step 1: Create Cloudinary Account
1. Go to https://cloudinary.com/users/register/free
2. Sign up for a free account (no credit card required)
3. After registration, you'll see your **Cloudinary Dashboard**

### Step 2: Get Your Credentials
From your Cloudinary Dashboard, you'll see:
- **Cloud Name** (e.g., `dxxxxx`) - shown at the top
- **API Key** (e.g., `123456789012345`) - in the "Account Details" section
- **API Secret** (e.g., `abcdefghijklmnopqrstuvwxyz123456`) - click "Reveal" to see it

**Important:** Copy these values - you'll need them in the next step!

### Step 3: Add Credentials to Railway
1. Go to Railway Dashboard → Your Service (`jj_flowershop2`) → **Variables** tab
2. Click **"+ New Variable"** and add these three variables:
   ```
   CLOUDINARY_CLOUD_NAME=your_cloud_name_here
   CLOUDINARY_API_KEY=your_api_key_here
   CLOUDINARY_API_SECRET=your_api_secret_here
   ```
3. **DO NOT** add `FILESYSTEM_DISK` - the code will automatically detect Cloudinary credentials

### Step 4: Deploy
After adding the variables, Railway will automatically redeploy. The application will:
- ✅ Automatically detect Cloudinary credentials
- ✅ Switch to Cloudinary storage
- ✅ All new uploads will be stored in Cloudinary permanently
- ✅ Images will persist across all deployments

### Step 5: Verify It's Working
1. After deployment, upload a test image (product, banner, etc.)
2. Check your Cloudinary Dashboard → **Media Library**
3. You should see your uploaded images there!
4. Images will now persist even after redeployments

## Benefits
✅ **Images persist across deployments** - No more re-uploading!
✅ **Automatic image optimization** - Cloudinary optimizes images automatically
✅ **CDN delivery** - Images load faster worldwide
✅ **Free tier** - 25GB is usually enough for most applications
✅ **No code changes needed** - Works automatically once credentials are set

## How It Works
- When Cloudinary credentials are detected, the app automatically uses Cloudinary for storage
- All `Storage::disk('public')` calls will use Cloudinary
- Images are uploaded directly to Cloudinary
- Image URLs point to Cloudinary CDN
- Images are permanent and never lost

## Troubleshooting
If images still disappear:
1. Check Railway Variables - make sure all 3 Cloudinary variables are set correctly
2. Check Railway Deploy Logs - look for any Cloudinary errors
3. Verify credentials in Cloudinary Dashboard
4. Make sure variables are set in the correct service (not the database service)

## Alternative: Railway Volumes (If Available)
If Railway Volumes are available in your plan:
1. Create a Volume in Railway Dashboard
2. Mount it to `/var/www/html/backend/storage/app/public`
3. Images will persist in the volume

**Note:** Cloudinary is recommended because it's free, easier to set up, and provides additional benefits like CDN and optimization.
