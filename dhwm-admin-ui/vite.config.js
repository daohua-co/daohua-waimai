import fs from 'fs';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
// import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
  plugins: [
    // visualizer({ open: true }), // 分析依赖几依赖包大小
    vue(),
  ],
  base: '/admincp/',
  css: {
    devSourcemap: true
  },
  build: {
    sourcemap: false,
    outDir: '../dhwm-web/public/admincp',
    emptyOutDir: true,
    manifest: true, // 在 outDir 中生成 manifest.json
    chunkSizeWarningLimit: 1024, // K
    rollupOptions: {
      input: ['index.html', 'seller.html'],
      output: {
        manualChunks: {
          'vendor': ['axios', 'vue', 'vue-router', 'vuedraggable'],
          'vendor.ep': ['element-plus', '@element-plus/icons-vue'],
          'vendor.mce1': ['tinymce', '@tinymce/tinymce-vue', 'tinymce/icons/default', 'tinymce/models/dom'],
          'vendor.mce2': [
            'tinymce/themes/silver',
            'tinymce/plugins/code',
            'tinymce/plugins/image',
            'tinymce/plugins/media',
            'tinymce/plugins/link',
            'tinymce/plugins/preview',
            'tinymce/plugins/template',
            'tinymce/plugins/table',
            'tinymce/plugins/pagebreak',
            'tinymce/plugins/lists',
            'tinymce/plugins/advlist',
            'tinymce/plugins/quickbars',
            'tinymce/plugins/code/plugin.js',
            'tinymce/plugins/image/plugin.js',
            'tinymce/plugins/media/plugin.js',
            'tinymce/plugins/link/plugin.js',
            'tinymce/plugins/preview/plugin.js',
            'tinymce/plugins/template/plugin.js',
            'tinymce/plugins/table/plugin.js',
            'tinymce/plugins/pagebreak/plugin.js',
            'tinymce/plugins/lists/plugin.js',
            'tinymce/plugins/advlist/plugin.js',
            'tinymce/plugins/quickbars/plugin.js',
          ], // tinymce 全打成一个包文件太大，分2个包

          'lib': [
            'src/lib/index.js',
          ],
          'admin': getDirFilePaths('src/admin'),
          'seller': getDirFilePaths('src/seller'),
        }
      }
    }
  }
});

/**
 * 获取目录及其子目录的文件列表
 * @param path
 * @param files
 * @return {*[]}
 */
function getDirFilePaths(path, files = []) {
  const result = [];
  for (const item of fs.readdirSync(path)) {
    const itemPath = `${path}/${item}`;
    const stat = fs.statSync(itemPath);
    if (stat.isDirectory()) {
      // 文件夹
      getDirFilePaths(itemPath, files);
      continue;
    }

    if (!itemPath.endsWith('.js') && !itemPath.endsWith('.vue')) {
      continue;
    }

    // 文件
    files.push(itemPath);
  }
  return files;
}
