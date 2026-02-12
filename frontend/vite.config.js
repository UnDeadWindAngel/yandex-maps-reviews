import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    port: 8081,
    proxy: {
      '/api': {
        target: 'http://yandex-reviews-backend-nginx:80',
        changeOrigin: true,
        // Не перезаписывать /api
      },
      '/sanctum': {
        target: 'http://yandex-reviews-backend-nginx:80',
        changeOrigin: true,
      }
    }
  }
})
