# Use the official Node.js image as the base.
FROM node:22-alpine AS node_alpine

# Install vue/cli globally.
# RUN npm install -g @vue/cli

# Set working directory inside the container.
WORKDIR /app

# Copy package.json and package-lock.json to the container.
# COPY package*.json ./

# Install dependencies.
# RUN npm install --legacy-peer-deps

# Copy the Vue.js project files to the container.
# COPY . .

# FROM node_alpine AS app-vue_dev

# FROM node_alpine AS app-vue_prod

# Build the Vue.js app for production.
# RUN npm run build

# Default command: Run shell to allow interactive commands.
# CMD [ "sh" ]

# Start the application.
# CMD [ "npm", "run", "serve" ]

# Expose port 8080 for Vue dev server.
EXPOSE 8080
