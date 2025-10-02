#!/bin/bash

# Script to create sample test images for Guide Wizard seeder
# This runs inside the PHP container

set -e

echo "Creating sample test images..."

UPLOAD_DIR="/var/www/html/app/sprinkles/guide_wizard/uploads/images"

# Ensure directory exists
mkdir -p "$UPLOAD_DIR"

# Create simple colored placeholder images using ImageMagick
# If ImageMagick is not available, we'll create simple text files as placeholders

# Check if convert command exists
if command -v convert &> /dev/null; then
    echo "Using ImageMagick to create sample images..."

    # Student answer image (blue theme)
    convert -size 400x300 xc:lightblue \
        -pointsize 30 -fill darkblue \
        -gravity center -annotate +0+0 "Student" \
        "$UPLOAD_DIR/student_answer.jpg"

    # Professional answer image (green theme)
    convert -size 400x300 xc:lightgreen \
        -pointsize 30 -fill darkgreen \
        -gravity center -annotate +0+0 "Professional" \
        "$UPLOAD_DIR/professional_answer.jpg"

    # Task image 1 (University registration)
    convert -size 800x600 xc:lightyellow \
        -pointsize 40 -fill darkred \
        -gravity center -annotate +0+-50 "University" \
        -pointsize 30 -fill black \
        -gravity center -annotate +0+50 "Registration Office" \
        "$UPLOAD_DIR/university_registration.jpg"

    # Task image 2 (Job center)
    convert -size 800x600 xc:lightcyan \
        -pointsize 40 -fill darkblue \
        -gravity center -annotate +0+-50 "Job Center" \
        -pointsize 30 -fill black \
        -gravity center -annotate +0+50 "Employment Services" \
        "$UPLOAD_DIR/job_center.jpg"

    echo "✅ Sample images created successfully with ImageMagick"
else
    echo "ImageMagick not available, downloading placeholder images..."

    # Download placeholder images from a public source
    cd "$UPLOAD_DIR"

    # Using placeholder.com for sample images
    curl -o student_answer.jpg "https://via.placeholder.com/400x300/87CEEB/00008B?text=Student" 2>&1 || echo "Failed to download student image"
    curl -o professional_answer.jpg "https://via.placeholder.com/400x300/90EE90/006400?text=Professional" 2>&1 || echo "Failed to download professional image"
    curl -o university_registration.jpg "https://via.placeholder.com/800x600/FFFFE0/8B0000?text=University+Registration" 2>&1 || echo "Failed to download university image"
    curl -o job_center.jpg "https://via.placeholder.com/800x600/E0FFFF/00008B?text=Job+Center" 2>&1 || echo "Failed to download job center image"

    echo "✅ Sample images downloaded successfully"
fi

# Set permissions
chmod 775 "$UPLOAD_DIR"/*.jpg
chown -R www-data:www-data "$UPLOAD_DIR"

echo ""
echo "Sample images created:"
ls -lh "$UPLOAD_DIR"

echo ""
echo "Image filenames for seeder:"
echo "  - student_answer.jpg"
echo "  - professional_answer.jpg"
echo "  - university_registration.jpg"
echo "  - job_center.jpg"
