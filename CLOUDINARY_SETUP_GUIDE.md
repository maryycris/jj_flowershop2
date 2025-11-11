# 🚀 CLOUDINARY SETUP GUIDE - STEP BY STEP

## IMPORTANTE: Kailangan nimo i-configure ang 3 ka Environment Variables sa Railway

### STEP 1: Kuhaa ang Cloudinary Credentials

1. **Adto sa Cloudinary Dashboard:**
   - Open: https://cloudinary.com/console
   - Login sa imong Cloudinary account

2. **Kuhaa ang 3 ka values gikan sa Dashboard:**
   - **Cloud Name** - makita sa top-right corner sa dashboard
   - **API Key** - makita sa "Account Details" section
   - **API Secret** - makita sa "Account Details" section (click "Reveal" para makita)

### STEP 2: I-add ang Environment Variables sa Railway

1. **Adto sa Railway Dashboard:**
   - Open: https://railway.app
   - Select ang imong project: `jj_flowershop2`

2. **Click "Variables" tab** (sa left sidebar)

3. **I-add ang 3 ka Environment Variables:**

   **Variable 1:**
   - **Name:** `CLOUDINARY_CLOUD_NAME`
   - **Value:** (i-paste ang Cloud Name gikan sa Cloudinary)
   - Example: `dd1tm1i6n`

   **Variable 2:**
   - **Name:** `CLOUDINARY_API_KEY`
   - **Value:** (i-paste ang API Key gikan sa Cloudinary)
   - Example: `123456789012345`

   **Variable 3:**
   - **Name:** `CLOUDINARY_API_SECRET`
   - **Value:** (i-paste ang API Secret gikan sa Cloudinary)
   - Example: `abcdefghijklmnopqrstuvwxyz123456`

4. **IMPORTANTE:**
   - **DILI** i-add ang `CLOUDINARY_URL` kung wala ka sure
   - Kung naa na ang `CLOUDINARY_URL` pero dili mo-start sa `cloudinary://`, **DELETE** nimo
   - Kung naa na ang `CLOUDINARY_URL` nga naka-set sa `APP_URL`, **DELETE** nimo

### STEP 3: I-redeploy ang Application

1. **After ma-add ang environment variables:**
   - Railway will automatically redeploy
   - OR manual redeploy: Click "Deployments" → "Redeploy"

2. **Wait until ma-complete ang deployment**

### STEP 4: I-test ang Image Upload ug Delete

1. **After ma-redeploy:**
   - Open ang admin panel: `https://jjflowershop2-production-c6db.up.railway.app/admin/products`
   - Try mag-upload og image sa product
   - Try mag-delete og image sa product

2. **Check ang logs kung naa pa error:**
   - Railway Dashboard → "Logs" tab
   - Look for "Cloudinary storage ENABLED" message

---

## ✅ CHECKLIST - I-verify nimo:

- [ ] Na-add na ang `CLOUDINARY_CLOUD_NAME` sa Railway Variables
- [ ] Na-add na ang `CLOUDINARY_API_KEY` sa Railway Variables  
- [ ] Na-add na ang `CLOUDINARY_API_SECRET` sa Railway Variables
- [ ] Wala na ang `CLOUDINARY_URL` kung naka-set sa wrong value (dili `cloudinary://`)
- [ ] Na-redeploy na ang application
- [ ] Na-test na ang image upload - working na
- [ ] Na-test na ang image delete - working na

---

## ❌ COMMON MISTAKES (AYAW BUHATA):

1. **DILI** i-set ang `CLOUDINARY_URL` sa `APP_URL` (e.g., `https://jjflowershop2-production...`)
2. **DILI** i-set ang `CLOUDINARY_URL` kung wala ka sure unsa ang correct format
3. **DILI** kalimtan ang 3 ka required variables (CLOUD_NAME, API_KEY, API_SECRET)

---

## 📝 NOTES:

- Ang code na-automatically mo-detect kung na-configure na ang Cloudinary
- Kung naa na ang 3 ka variables, automatic na mo-use og Cloudinary
- Kung wala, mo-fallback sa local storage (ma-delete ang images after deployment)
- Ang images ma-save sa Cloudinary folder: `catalog_products/`

---

## 🆘 TROUBLESHOOTING:

**Kung dili gihapon mo-work:**

1. **Check ang Railway Logs:**
   - Look for: "Cloudinary storage ENABLED"
   - Kung makita ang "Cloudinary not configured", wala pa nimo na-add ang variables

2. **Verify ang Environment Variables:**
   - Railway Dashboard → Variables tab
   - I-check kung naa ba ang 3 ka variables
   - I-check kung correct ba ang values (no extra spaces)

3. **Check ang Cloudinary Dashboard:**
   - I-verify kung correct ba ang credentials
   - I-check kung active pa ba ang account

