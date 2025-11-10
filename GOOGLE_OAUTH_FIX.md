# Google OAuth redirect_uri_mismatch Fix

## Problem
Error: `redirect_uri_mismatch` - Ang redirect URI na ginagamit ng app ay hindi match sa naka-configure sa Google Cloud Console.

## Solution

### Step 1: I-verify ang Redirect URI sa Google Cloud Console

1. Pumunta sa [Google Cloud Console](https://console.cloud.google.com/)
2. Pumunta sa **APIs & Services** > **Credentials**
3. I-click ang iyong OAuth 2.0 Client ID
4. Sa **Authorized redirect URIs**, i-verify na may EXACT match:
   ```
   https://jjflowershop2-production.up.railway.app/auth/google/callback
   ```

### Step 2: Important Notes

**CRITICAL: Dapat EXACT match!**

✅ **CORRECT:**
- `https://jjflowershop2-production.up.railway.app/auth/google/callback`

❌ **WRONG (hindi gagana):**
- `http://jjflowershop2-production.up.railway.app/auth/google/callback` (HTTP instead of HTTPS)
- `https://jjflowershop2-production.up.railway.app/auth/google/callback/` (may trailing slash)
- `https://jjflowershop2-production.up.railway.app/auth/google/callback ` (may space)
- `https://jjflowershop2-production.up.railway.app/Auth/Google/Callback` (different case)

### Step 3: I-update ang Google Cloud Console

1. Sa **Authorized redirect URIs** section:
   - I-delete ang lahat ng existing redirect URIs (kung may mali)
   - I-click **+ ADD URI**
   - I-type EXACTLY: `https://jjflowershop2-production.up.railway.app/auth/google/callback`
   - **WALANG trailing slash!**
   - **WALANG spaces!**
   - **Dapat HTTPS!**
2. I-click **SAVE**
3. Maghintay ng 1-2 minutes para mag-take effect

### Step 4: I-verify sa Railway

1. Pumunta sa Railway → jj_flowershop2 → **Variables**
2. I-verify na ang `GOOGLE_REDIRECT_URI` ay:
   ```
   https://jjflowershop2-production.up.railway.app/auth/google/callback
   ```
3. Dapat walang trailing slash!

### Step 5: I-test ulit

1. Maghintay ng 1-2 minutes pagkatapos mag-save sa Google Cloud Console
2. I-try ang Google login ulit
3. Dapat gumana na!

## Troubleshooting

Kung hindi pa rin gumagana:

1. **I-check ang Railway Logs** - Tingnan kung ano ang exact redirect URI na ginagamit
2. **I-copy-paste ang redirect URI** - Huwag i-type manually, i-copy-paste mo lang para walang typo
3. **I-clear ang browser cache** - Minsan naka-cache ang old redirect
4. **I-verify ang APP_URL** - Dapat `https://jjflowershop2-production.up.railway.app` (walang trailing slash)

## Common Mistakes

1. ❌ May trailing slash sa Google Console pero wala sa code
2. ❌ May trailing slash sa code pero wala sa Google Console
3. ❌ HTTP instead of HTTPS
4. ❌ May typo sa domain name
5. ❌ May spaces sa redirect URI

## Verification Checklist

- [ ] Google Cloud Console → Authorized redirect URIs → May exact match
- [ ] Railway Variables → `GOOGLE_REDIRECT_URI` → Exact match
- [ ] Railway Variables → `APP_URL` → `https://jjflowershop2-production.up.railway.app` (walang trailing slash)
- [ ] Naghintay ng 1-2 minutes pagkatapos mag-save
- [ ] I-clear ang browser cache
- [ ] I-try ulit ang Google login

