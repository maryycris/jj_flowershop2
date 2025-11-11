# 🔑 USE EXISTING TOKEN - REGENERATE

## Option 1: Regenerate Existing Token (RECOMMENDED)

1. **Sa GitHub page nga naa ka:**
   - Click ang **"JJ Flowershop Railway — repo"** token (blue text)
   - OR click **"Regenerate"** button kung naa

2. **Kung wala ang "Regenerate" button:**
   - Click **"Generate new token"** → **"Generate new token (classic)"**
   - Same settings: **"repo"** scope
   - I-name: `jj_flowershop2-deploy` (or same name)

3. **I-copy ang token** (makita lang ni once!)

---

## Option 2: Clear Cached Credential ug Use New Token

1. **Clear ang old credential:**
   ```powershell
   cmdkey /delete:LegacyGeneric:target=git:https://github.com
   ```

2. **Generate new token** (same steps sa taas)

3. **I-push gamit ang new token:**
   ```powershell
   git remote set-url origin https://YOUR_NEW_TOKEN@github.com/maryycris/jj_flowershop2.git
   git push origin main
   ```

---

## ⚠️ IMPORTANTE:

- Ang token value **dili makita** after mo-generate
- Kung wala nimo na-save, kailangan mo-regenerate
- Ang old token mo-stop working after mo-regenerate

