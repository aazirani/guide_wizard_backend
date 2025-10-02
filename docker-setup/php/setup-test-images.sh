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

    # Task image (Registration Process)
    convert -size 800x600 xc:lightyellow \
        -pointsize 40 -fill darkred \
        -gravity center -annotate +0+0 "Registration Process" \
        "$UPLOAD_DIR/registration_process.jpg"

    # Step 1 image (Questions)
    convert -size 600x400 xc:lavender \
        -pointsize 50 -fill purple \
        -gravity center -annotate +0+0 "Questions" \
        "$UPLOAD_DIR/step_questions.jpg"

    # Step 2 image (Tasks)
    convert -size 600x400 xc:lightcoral \
        -pointsize 50 -fill darkred \
        -gravity center -annotate +0+0 "Your Tasks" \
        "$UPLOAD_DIR/step_tasks.jpg"

    echo "✅ Sample images created successfully with ImageMagick"
else
    echo "ImageMagick not available, downloading placeholder images..."

    # Download placeholder images from a public source
    cd "$UPLOAD_DIR"

    # Using placeholder.com for sample images
    curl -o student_answer.jpg "https://via.placeholder.com/400x300/87CEEB/00008B?text=Student" 2>&1 || echo "Failed to download student image"
    curl -o professional_answer.jpg "https://via.placeholder.com/400x300/90EE90/006400?text=Professional" 2>&1 || echo "Failed to download professional image"
    curl -o registration_process.jpg "https://via.placeholder.com/800x600/FFFFE0/8B0000?text=Registration+Process" 2>&1 || echo "Failed to download registration image"
    curl -o step_questions.jpg "https://via.placeholder.com/600x400/E6E6FA/800080?text=Questions" 2>&1 || echo "Failed to download questions step image"
    curl -o step_tasks.jpg "https://via.placeholder.com/600x400/F08080/8B0000?text=Your+Tasks" 2>&1 || echo "Failed to download tasks step image"

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
echo "  - student_answer.jpg (Answer 1)"
echo "  - professional_answer.jpg (Answer 2)"
echo "  - registration_process.jpg (Task)"
echo "  - step_questions.jpg (Step 1)"
echo "  - step_tasks.jpg (Step 2)"
