# Social Login Setup para sa Railway

## Requirements

Para gumana ang Facebook at Google login sa Railway, kailangan mo i-configure ang mga sumusunod:

### 1. Railway Environment Variables

I-add ang mga sumusunod na environment variables sa Railway:

#### Facebook OAuth:
```
FACEBOOK_CLIENT_ID=769015785952499
FACEBOOK_CLIENT_SECRET=e3751172c5bf6451c8f2ed10656abfb0
FACEBOOK_REDIRECT_URI=https://jjflowershop2-production.up.railway.app/auth/facebook/callback
```

#### Google OAuth:
```
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://jjflowershop2-production.up.railway.app/auth/google/callback
```

### 2. Facebook App Configuration

1. Pumunta sa [Facebook Developers Console](https://developers.facebook.com/)
2. Piliin ang iyong app (Client ID: 769015785952499)
3. Pumunta sa **Settings** > **Basic**
4. I-verify na ang **App Domains** ay may:
   - `jjflowershop2-production.up.railway.app`
5. Pumunta sa **Products** > **Facebook Login** > **Settings**
6. Sa **Valid OAuth Redirect URIs**, i-add:
   - `https://jjflowershop2-production.up.railway.app/auth/facebook/callback`
7. I-save ang changes

### 3. Google OAuth Configuration

**IMPORTANTE: Kailangan mo muna gumawa ng Google OAuth credentials!**

#### Step 1: Gumawa ng Google OAuth Credentials

1. Pumunta sa [Google Cloud Console](https://console.cloud.google.com/)
2. Pumunta sa **APIs & Services** > **Credentials**
3. Click **+ CREATE CREDENTIALS** > **OAuth client ID**
4. Kung first time, kailangan mo muna i-configure ang **OAuth consent screen**:
   - Pumunta sa **OAuth consent screen**
   - Piliin ang **External** (para sa public users)
   - I-fill up ang required fields (App name, User support email, Developer contact)
   - I-save
5. Bumalik sa **Credentials** > **+ CREATE CREDENTIALS** > **OAuth client ID**
6. Piliin ang **Application type**: **Web application**
7. I-bigay ang **Name**: "JJ Flowershop Railway"
8. Sa **Authorized redirect URIs**, i-click **+ ADD URI** at i-add:
   - `https://jjflowershop2-production.up.railway.app/auth/google/callback`
9. I-click **CREATE**
10. I-copy ang **Client ID** at **Client Secret** (important: i-save mo ito agad, hindi na ito ma-view ulit!)

#### Step 2: I-set ang Credentials sa Railway

1. Pumunta sa Railway → jj_flowershop2 → **Variables**
2. I-add ang mga sumusunod:
   ```
   GOOGLE_CLIENT_ID=<paste_your_client_id_here>
   GOOGLE_CLIENT_SECRET=<paste_your_client_secret_here>
   GOOGLE_REDIRECT_URI=https://jjflowershop2-production.up.railway.app/auth/google/callback
   ```
3. I-save ang changes
4. Mag-redeploy ang application

### 4. Important Notes

- **APP_URL** dapat naka-set sa Railway environment variables:
  ```
  APP_URL=https://jjflowershop2-production.up.railway.app
  ```

- Ang redirect URIs ay dapat **exact match** sa naka-configure sa Facebook/Google developer consoles

- Pagkatapos mag-update ng OAuth settings, maghintay ng ilang minuto bago mag-take effect

### 5. Testing

Pagkatapos i-configure:

1. I-deploy ulit ang application sa Railway
2. I-test ang Facebook login
3. I-test ang Google login
4. I-check ang Railway Deploy Logs kung may errors

### Troubleshooting

Kung hindi pa rin gumagana:

1. **Check Railway Logs** - Tingnan ang Deploy Logs para sa OAuth errors
2. **Verify Redirect URIs** - Dapat exact match sa Facebook/Google settings
3. **Check Environment Variables** - I-verify na naka-set ang lahat ng credentials
4. **Facebook/Google Console** - I-verify na naka-enable ang OAuth at naka-set ang redirect URIs

