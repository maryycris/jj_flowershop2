# Persistent Image Storage Setup - Cloudinary

## Problem
Railway uses an **ephemeral filesystem** - uploaded images are lost when the container restarts or redeploys.

## Solution: Cloudinary (Free Tier Available)

Cloudinary provides persistent cloud storage for images. The free tier includes:
- 25 GB storage
- 25 GB monthly bandwidth
- Perfect for small to medium applications

## Setup Instructions

### Step 1: Create Cloudinary Account
1. Go to https://cloudinary.com/users/register/free
2. Sign up for a free account
3. After registration, you'll see your **Cloudinary Dashboard**

### Step 2: Get Your Credentials
From your Cloudinary Dashboard, copy:
- **Cloud Name** (e.g., `dxxxxx`)
- **API Key** (e.g., `123456789012345`)
- **API Secret** (e.g., `abcdefghijklmnopqrstuvwxyz123456`)

### Step 3: Add Credentials to Railway
1. Go to Railway Dashboard → Your Service → Variables
2. Add these environment variables:
   ```
   CLOUDINARY_CLOUD_NAME=your_cloud_name
   CLOUDINARY_API_KEY=your_api_key
   CLOUDINARY_API_SECRET=your_api_secret
   FILESYSTEM_DISK=cloudinary
   ```

### Step 4: Deploy
After adding the variables, Railway will automatically redeploy and images will be stored in Cloudinary permanently!

## Benefits
✅ Images persist across deployments
✅ No need to re-upload images
✅ Automatic image optimization
✅ CDN delivery (faster loading)
✅ Free tier is sufficient for most applications

## Alternative: Railway Volumes (If Available)
If Railway Volumes are available in your plan:
1. Create a Volume in Railway Dashboard
2. Mount it to `/var/www/html/backend/storage/app/public`
3. Images will persist in the volume

