# Neofala Image Generator

## 1. Overview
The **AI Photo Studio Web App** enables users to transform simple product photos into professional, visually compelling marketing assets using AI. The tool allows users to upload product images, apply a reference style, adjust creative controls, and generate high-quality product visuals through **OpenRouter AI**.

---

## 2. Goals & Objectives
- Provide an intuitive web interface for marketers, designers, and e-commerce sellers to create studio-like product photos.  
- Allow creative flexibility with **style references** and **customizable controls** (aspect ratio, lighting, perspective, prompts).  
- Use **OpenRouter AI** as the image generation engine.  
- Deliver fast, high-resolution outputs suitable for marketing campaigns and online stores.

---

## 3. Target Users
- **E-commerce sellers** (Amazon, Shopee, Tokopedia, Shopify merchants).  
- **Marketing teams** seeking quick visual assets.  
- **Content creators** who want branded product imagery.  

---

## 4. Features & Requirements

### 4.1 Uploads
- **Product Image (Required)**: Single image of product in PNG/JPG.  
- **Style Reference (Optional)**: Image to guide mood, lighting, and composition.  

### 4.2 Creative Controls
- **Aspect Ratio**: Presets (1:1, 3:4, 16:9, custom).  
- **Lighting Style**: Options such as *Bright Studio, Moody & Cinematic, Natural Light*.  
- **Camera Perspective**: Options such as *Heroic Low-Angle, Eye-Level, Top-Down*.  
- **Additional Prompt Details**: Free-text field for custom instructions.  

### 4.3 Prompt Generation
System constructs a **final structured prompt** combining:  
- Product photo as the subject  
- Style reference aesthetic (if provided)  
- Selected creative controls  
- Additional prompt details  

**Example Prompt:**  
Task: Generate a new image based on the provided product photo.
Primary Subject: The object in the first uploaded image.
Creative Direction: Aspect Ratio: 3:4.
Lighting Style: Moody and Cinematic.
Camera Perspective: Heroic Low-Angle.
Additional Instructions: Add bucket of real oranges to add background component to the photoshoot.
Take strong inspiration from the provided style reference image, matching its overall aesthetic, color palette, mood, and texture.
Output a single, high-resolution image without any text, watermarks, or logos.

### 4.4 Output
- **High-resolution generated image** (PNG/JPG).  
- Ability to **download** results.  
- **Generation history** stored per session (showing inputs, settings, final prompt, and results).  

---

## 5. Technical Requirements

### 5.1 Frontend
- Framework: **PHP**  
- UI Components: TailwindCSS + ShadCN for styling  
- Upload via drag-and-drop or file picker  

### 5.2 Backend
- API Integration: **OpenRouter AI API** for text-to-image generation  
- Prompt construction logic before API request  
- Store temporary session data (no permanent storage unless account system added)  

### 5.3 Image Processing
- Pre-upload validation (size, format)  
- Optimize images before sending to API  

### 5.4 Security
- File sanitization on upload  
- Rate limiting on API requests  

---

## 6. User Flow
1. User uploads **product photo**.  
2. (Optional) User uploads **style reference**.  
3. User sets **creative controls**.  
4. User clicks **Generate Image**.  
5. App constructs final prompt → sends request to **OpenRouter AI**.  
6. AI returns generated image → displayed in preview panel.  
7. User can **download** image or view in **generation history**.  

---

## 7. Success Metrics
- Time to generate image (<20 seconds).  
- User satisfaction (measured by downloads per session).  
- Retention (returning users generating multiple images).  

---

## 8. Future Enhancements
- Multiple style references support.  
- Batch generation (multiple outputs in one request).  
- User accounts with saved projects.  
- Templates library (preset creative controls).  
