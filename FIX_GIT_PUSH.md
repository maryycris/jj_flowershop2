# 🔧 FIX GIT PUSH PERMISSION ISSUE

## Problem:
- Git credentials cached para sa wrong user (`josephusared`)
- Repository belongs to `maryycris`
- Need to re-authenticate

## SOLUTION: Use Personal Access Token (PAT)

### STEP 1: Create GitHub Personal Access Token

1. **Adto sa GitHub:**
   - Open: https://github.com/settings/tokens
   - Click "Generate new token" → "Generate new token (classic)"

2. **I-configure ang token:**
   - **Note:** `jj_flowershop2-deploy`
   - **Expiration:** 90 days (or longer)
   - **Scopes:** Check `repo` (full control of private repositories)

3. **Click "Generate token"**
4. **I-copy ang token** (makita lang ni once!)

### STEP 2: Clear Cached Credentials

**Option A: Via Windows Credential Manager (EASIEST)**

1. Press `Windows Key + R`
2. Type: `control /name Microsoft.CredentialManager`
3. Press Enter
4. Click "Windows Credentials"
5. Look for: `git:https://github.com`
6. Click "Remove" or "Edit" → Delete

**Option B: Via Command (ALTERNATIVE)**

```powershell
git credential-manager erase
```

### STEP 3: Push with Token

**Option 1: Update Remote URL with Token (RECOMMENDED)**

```powershell
# Replace YOUR_TOKEN with the token you copied
git remote set-url origin https://YOUR_TOKEN@github.com/maryycris/jj_flowershop2.git
git push origin main
```

**Option 2: Push and Enter Credentials When Prompted**

```powershell
git push origin main
# When prompted:
# Username: maryycris
# Password: (paste your Personal Access Token here, NOT your GitHub password)
```

### STEP 4: Verify Push

After successful push:
- Check GitHub: https://github.com/maryycris/jj_flowershop2
- Railway will auto-deploy (check Railway Dashboard)

---

## ⚠️ IMPORTANT NOTES:

1. **Personal Access Token** = Password when pushing via HTTPS
2. **DILI** i-share ang token sa anyone
3. Kung expired na ang token, create new one and update remote URL

---

## 🚀 QUICK FIX (If you already have a token):

```powershell
# Clear credentials
git credential-manager erase

# Update remote with token (replace YOUR_TOKEN)
git remote set-url origin https://YOUR_TOKEN@github.com/maryycris/jj_flowershop2.git

# Push
git push origin main
```

