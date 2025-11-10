# ⚠️ URGENT: Fix Image Persistence Issue

## Problem
Your images are disappearing because Railway uses **ephemeral storage** - files are deleted on every deployment.

## Quick Fix (5 minutes)

### Step 1: Create Free Cloudinary Account
1. Go to: **https://cloudinary.com/users/register/free**
2. Sign up (no credit card needed)
3. After signup, you'll see your Dashboard

### Step 2: Get Your Credentials
In your Cloudinary Dashboard:
- **Cloud Name**: Shown at the top (e.g., `dxxxxx`)
- **API Key**: In "Account Details" section
- **API Secret**: Click "Reveal" button to see it

### Step 3: Add to Railway (IMPORTANT!)
1. Go to **Railway Dashboard**
2. Click on your service: **`jj_flowershop2`**
3. Go to **Variables** tab
4. Click **"+ New Variable"** and add these **3 variables**:

```
CLOUDINARY_CLOUD_NAME=your_cloud_name_here
CLOUDINARY_API_KEY=your_api_key_here
CLOUDINARY_API_SECRET=your_api_secret_here
```

**Replace the values with your actual Cloudinary credentials!**

### Step 4: Wait for Deployment
- Railway will automatically redeploy
- Check the deploy logs - you should see: "✅ Cloudinary is CONFIGURED"
- If you see "⚠️ WARNING: Cloudinary is NOT configured", check your variables

### Step 5: Test
1. Upload a new image (product, banner, etc.)
2. Check Cloudinary Dashboard → **Media Library**
3. You should see your image there!
4. Redeploy your app - the image will still be there! ✅

## That's It!
Once configured, **all images will persist forever** - no more re-uploading!

## Need Help?
- Check Railway Deploy Logs for Cloudinary status
- Verify all 3 variables are set correctly
- Make sure variables are in the `jj_flowershop2` service (not database service)

