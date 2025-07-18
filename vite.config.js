import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import fs from 'fs';
import UnoCSS from 'unocss/vite'; // 1. Import UnoCSS
import svgLoader from 'vite-svg-loader';

// https://vitejs.dev/config/
export default defineConfig({
	plugins: [
		vue(),
		UnoCSS(), // 2. Add the UnoCSS plugin
		mkcert(),
		svgLoader(),
		{
			name: 'wordpress-vite-helper',
			configureServer(server) {
				server.httpServer.on('listening', () => {
					const devFile = '.vite/development';
					if (!fs.existsSync('.vite')) {
						fs.mkdirSync('.vite');
					}
					fs.writeFileSync(devFile, 'Vite is running.');
					process.on(
						'exit',
						() => fs.existsSync(devFile) && fs.unlinkSync(devFile)
					);
					process.on('SIGINT', process.exit);
					process.on('SIGTERM', process.exit);
				});
			},
		},
	],
	build: {
		manifest: true,
		outDir: 'dist',
		rollupOptions: {
			input: 'src/main.js',
		},
	},
	server: {
		https: true,
		host: 'localhost',
		port: 5173,
		strictPort: true,
		cors: true,
		hmr: {
			host: 'localhost',
		},
	},
});
