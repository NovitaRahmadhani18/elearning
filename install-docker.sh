#!/bin/bash

# Quick Docker & Docker Compose installer
echo "🐳 Installing Docker dan Docker Compose..."

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
rm get-docker.sh

# Install Docker Compose Plugin
sudo apt-get update
sudo apt-get install -y docker-compose-plugin

echo "✅ Docker dan Docker Compose berhasil diinstall!"
echo "⚠️  Logout dan login kembali untuk menggunakan Docker tanpa sudo"
echo "💡 Atau jalankan: newgrp docker"

# Test installation
echo "🧪 Testing installation..."
docker --version
docker compose version

echo ""
echo "🚀 Sekarang jalankan:"
echo "   newgrp docker"
echo "   ./deploy-production.sh yourdomain.com"
