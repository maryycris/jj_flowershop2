# 🔑 HOW TO GET GITHUB PERSONAL ACCESS TOKEN

## STEP-BY-STEP GUIDE:

### STEP 1: Adto sa GitHub Settings

1. **Open ang browser**
2. **Login sa GitHub** (kung wala pa)
3. **Click sa imong profile picture** (top-right corner)
4. **Click "Settings"**

### STEP 2: Adto sa Developer Settings

1. **Scroll down** sa left sidebar
2. **Click "Developer settings"** (sa bottom)
3. **Click "Personal access tokens"**
4. **Click "Tokens (classic)"**

### STEP 3: Generate New Token

1. **Click "Generate new token"** button
2. **Click "Generate new token (classic)"** (dili "fine-grained")

### STEP 4: I-configure ang Token

1. **Note:** Type: `jj_flowershop2-deploy` (or any name you want)
2. **Expiration:** Select "90 days" (or longer)
3. **Scopes:** 
   - ✅ Check **"repo"** (Full control of private repositories)
   - This will automatically check other repo-related permissions

### STEP 5: Generate ug Copy

1. **Scroll down** → Click **"Generate token"** button (green button)
2. **IMPORTANTE:** I-copy ang token **KARON** kay makita lang ni once!
   - Ang token looks like: `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - I-copy ang ENTIRE token

### STEP 6: I-save ang Token

- **I-paste sa notepad** temporarily
- **DILI** i-share sa anyone
- **DILI** i-commit sa git

---

## 📍 DIRECT LINK:

**Quick access:** https://github.com/settings/tokens/new

---

## ⚠️ IMPORTANTE:

- Ang token makita lang **ONCE** after mo-generate
- Kung nawala, kailangan mo-create og new one
- Ang token gamiton as "password" when pushing via HTTPS

---

## 🎯 AFTER MAKUHA ANG TOKEN:

I-run niining command (i-replace ang `YOUR_TOKEN`):

```powershell
git remote set-url origin https://YOUR_TOKEN@github.com/maryycris/jj_flowershop2.git
git push origin main
```

---

## 🆘 KUNG DILI MAKITA ANG "Developer settings":

1. Make sure naka-login ka sa correct GitHub account
2. Try direct link: https://github.com/settings/tokens
3. Kung wala gihapon, i-check kung naa ba ka permission sa repository

