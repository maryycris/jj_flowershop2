# Google OAuth Verification Checklist

## Current Status
✅ Redirect URI sa Google Cloud Console: `https://jjflowershop2-production.up.railway.app/auth/google/callback`
✅ Redirect URI sa app logs: `https://jjflowershop2-production.up.railway.app/auth/google/callback`
✅ Parehong match!

## Verification Steps

### 1. I-verify ang Client ID

**Sa Google Cloud Console:**
- Client ID: `666781026809-kv8g8mk3km0ur878dfmthpgoktmoft94.apps.googleusercontent.com`

**Sa Railway Variables:**
- `GOOGLE_CLIENT_ID` dapat: `666781026809-kv8g8mk3km0ur878dfmthpgoktmoft94.apps.googleusercontent.com`

**I-verify:**
1. Pumunta sa Railway → jj_flowershop2 → **Variables**
2. I-check ang `GOOGLE_CLIENT_ID`
3. Dapat EXACT match sa Google Cloud Console

### 2. I-verify ang Client Secret

**Important:** Hindi mo na makikita ang full client secret sa Google Cloud Console (naka-mask na).

**Options:**
- Option A: Kung may copy ka pa ng original client secret, i-verify na match sa Railway
- Option B: Gumawa ng bagong client secret:
  1. Sa Google Cloud Console → **Client secrets** section
  2. I-click **+ Add secret**
  3. I-copy ang bagong secret
  4. I-update sa Railway → `GOOGLE_CLIENT_SECRET`

### 3. I-verify ang Redirect URI

**Sa Google Cloud Console:**
- `https://jjflowershop2-production.up.railway.app/auth/google/callback` ✅

**Sa Railway Variables:**
- `GOOGLE_REDIRECT_URI` dapat: `https://jjflowershop2-production.up.railway.app/auth/google/callback`

**I-verify:**
1. Railway → jj_flowershop2 → **Variables**
2. I-check ang `GOOGLE_REDIRECT_URI`
3. Dapat EXACT match

### 4. I-verify ang APP_URL

**Sa Railway Variables:**
- `APP_URL` dapat: `https://jjflowershop2-production.up.railway.app`
- **WALANG trailing slash!**

### 5. Common Issues & Solutions

#### Issue 1: Client Secret Mismatch
**Symptom:** `redirect_uri_mismatch` o `invalid_client`
**Solution:**
1. Gumawa ng bagong client secret sa Google Cloud Console
2. I-update ang `GOOGLE_CLIENT_SECRET` sa Railway
3. Mag-redeploy

#### Issue 2: Client ID Mismatch
**Symptom:** `invalid_client`
**Solution:**
1. I-verify na ang `GOOGLE_CLIENT_ID` sa Railway ay match sa Google Cloud Console
2. I-copy-paste para walang typo

#### Issue 3: Cached Redirect
**Symptom:** Still getting errors kahit tama na lahat
**Solution:**
1. I-clear ang browser cache
2. I-try sa incognito/private window
3. Maghintay ng 5-10 minutes (Google's cache)

#### Issue 4: OAuth Consent Screen Not Configured
**Symptom:** `access_denied` o `invalid_client`
**Solution:**
1. Pumunta sa Google Cloud Console → **OAuth consent screen**
2. I-verify na naka-configure na
3. I-verify na naka-set ang **User type** to **External**
4. I-save

### 6. Testing Steps

1. **I-verify ang credentials:**
   - [ ] `GOOGLE_CLIENT_ID` = `666781026809-kv8g8mk3km0ur878dfmthpgoktmoft94.apps.googleusercontent.com`
   - [ ] `GOOGLE_CLIENT_SECRET` = naka-set (hindi empty)
   - [ ] `GOOGLE_REDIRECT_URI` = `https://jjflowershop2-production.up.railway.app/auth/google/callback`
   - [ ] `APP_URL` = `https://jjflowershop2-production.up.railway.app`

2. **I-clear ang cache:**
   - I-clear ang browser cache
   - I-try sa incognito window

3. **I-test:**
   - I-click ang Google login button
   - Dapat mag-redirect sa Google sign-in page
   - Pagkatapos mag-login, dapat mag-redirect pabalik sa app

### 7. Debugging

Kung hindi pa rin gumagana, i-check ang Railway Deploy Logs:
- Tingnan kung may error messages
- I-verify na ang `client_id_set` at `client_secret_set` ay `true`
- I-verify na ang `redirect_uri` ay tama

### 8. Quick Fix: Generate New Client Secret

Kung hindi mo na makita ang client secret:

1. **Google Cloud Console:**
   - Pumunta sa **APIs & Services** > **Credentials**
   - I-click ang iyong OAuth Client ID
   - Sa **Client secrets** section, i-click **+ Add secret**
   - I-copy ang bagong secret (i-save mo agad!)

2. **Railway:**
   - Pumunta sa **Variables**
   - I-update ang `GOOGLE_CLIENT_SECRET` sa bagong secret
   - I-save

3. **Redeploy:**
   - Mag-redeploy ang application
   - I-test ulit

