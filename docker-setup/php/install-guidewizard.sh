#!/bin/bash

# Guide Wizard Backend Installation Script (runs inside container)
# This script sets up UserFrosting 4.6 with the Guide Wizard sprinkle

set -e  # Exit on error

echo "🚀 Starting Guide Wizard Backend Installation..."
echo ""

# Configuration - UPDATE THESE VALUES
USERFROSTING_BRANCH="4.6"
USERFROSTING_REPO="https://github.com/userfrosting/UserFrosting.git"
GUIDE_WIZARD_REPO="https://github.com/aazirani/guide_wizard_backend.git"
GUIDE_WIZARD_BRANCH="master"  # UPDATE IF NEEDED

# Check if already installed
if [ -f "/var/www/html/app/sprinkles.json" ]; then
    echo "⚠️  UserFrosting appears to be already installed"
    echo "Files detected in /var/www/html/"
    echo ""
    read -p "Do you want to continue anyway? This may overwrite existing files (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Installation cancelled."
        exit 0
    fi
    echo ""
fi

# Step 1: Clone UserFrosting 4.6
echo "📦 Step 1: Cloning UserFrosting 4.6..."
cd /var/www/html

# Clean up if needed
if [ -d ".git" ]; then
    rm -rf .git
fi

# Clone into temp directory and move contents
git clone --branch "$USERFROSTING_BRANCH" "$USERFROSTING_REPO" /tmp/userfrosting
cp -r /tmp/userfrosting/. .
rm -rf /tmp/userfrosting
echo "✅ UserFrosting 4.6 cloned successfully"

# Step 2: Clone Guide Wizard sprinkle
echo ""
echo "📦 Step 2: Cloning Guide Wizard sprinkle..."
mkdir -p app/sprinkles
git clone --branch "$GUIDE_WIZARD_BRANCH" "$GUIDE_WIZARD_REPO" app/sprinkles/guide_wizard
echo "✅ Guide Wizard sprinkle cloned successfully"

# Step 3: Configure sprinkles.json
echo ""
echo "📦 Step 3: Configuring sprinkles.json..."
cat > app/sprinkles.json <<'EOF'
{
    "base": [
        "core",
        "account",
        "admin",
        "FormGenerator",
        "guide_wizard"
    ],
    "require": {
        "lcharette/uf_formgenerator": "^4.0.0"
    }
}
EOF
echo "✅ sprinkles.json configured"

# Step 4: Set up environment file
echo ""
echo "📦 Step 4: Setting up .env file..."
cp app/.env.example app/.env

# Get database credentials from environment variables
if [ -n "$DB_HOST" ] && [ -n "$DB_NAME" ]; then
    sed -i "s/DB_DRIVER=.*/DB_DRIVER=mysql/" app/.env
    sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" app/.env
    sed -i "s/DB_PORT=.*/DB_PORT=3306/" app/.env
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" app/.env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" app/.env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" app/.env
    echo "✅ Database configuration updated from environment variables"
else
    echo "⚠️  Database environment variables not found, using defaults"
    echo "⚠️  You may need to manually configure app/.env"
fi

# Step 5: Set permissions
echo ""
echo "📦 Step 5: Setting directory permissions..."
mkdir -p app/cache app/logs app/sessions app/storage
mkdir -p app/sprinkles/guide_wizard/uploads/images
chmod -R 775 app/cache app/logs app/sessions app/storage app/sprinkles/guide_wizard/uploads
echo "✅ Permissions set"

# Step 6: Install Composer dependencies
echo ""
echo "📦 Step 6: Installing Composer dependencies (this may take a few minutes)..."
composer install --no-interaction --prefer-dist
echo "✅ Composer dependencies installed"

# Step 7: Install Node.js dependencies
#echo ""
#echo "📦 Step 7: Installing Node.js dependencies (this may take a few minutes)..."
#npm install
#echo "✅ Node.js dependencies installed"

# Step 8: Run database migrations
echo ""
echo "📦 Step 8: Running database migrations..."
echo "⚠️  Note: You'll need to create an admin user"
echo ""

# Check if database is ready
echo "Waiting for database to be ready..."
for i in {1..30}; do
    if php bakery debug 2>&1 | grep -q "success\|connected\|ready" || php -r "new PDO('mysql:host=$DB_HOST;dbname=$DB_NAME', '$DB_USER', '$DB_PASSWORD');" 2>/dev/null; then
        echo "✅ Database connection successful"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "⚠️  Could not verify database connection, but proceeding anyway..."
    fi
    sleep 2
done

# Run bakery bake
php bakery bake
echo "✅ Database migrations completed"

# Step 9: Seed the database with GuideWizardBase
echo ""
echo "📦 Step 9: Seeding database with Guide Wizard base data..."
php bakery seed GuideWizardBase
echo "✅ Base data seeded successfully"

# Step 10: Seed the database with test data
echo ""
echo "📦 Step 10: Seeding database with test data..."
php bakery seed GuideWizardTestData
echo "✅ Test data seeded successfully"

# Final summary
echo ""
echo "🎉 Guide Wizard Backend Installation Complete!"
echo ""
echo "📋 Summary:"
echo "   ✅ UserFrosting 4.6 installed"
echo "   ✅ Guide Wizard sprinkle installed"
echo "   ✅ FormGenerator 4.0 installed"
echo "   ✅ Database configured and migrated"
echo "   ✅ Base data seeded"
echo "   ✅ Test data seeded"
echo ""
echo "🌐 Your application should now be accessible via your configured domain"
echo ""
