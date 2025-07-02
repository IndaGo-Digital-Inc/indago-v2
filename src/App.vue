<template>
	<div class="font-sans bg-gray-50 min-h-screen">
		<header class="bg-white shadow-sm">
			<nav class="container mx-auto p-4 flex justify-center space-x-6">
				<router-link
					to="/"
					class="text-gray-700 hover:text-blue-600 font-medium"
					>Home</router-link
				>
				<router-link
					to="/services"
					class="text-gray-700 hover:text-blue-600 font-medium"
					>Services</router-link
				>
				<router-link
					to="/portfolio"
					class="text-gray-700 hover:text-blue-600 font-medium"
					>Portfolio</router-link
				>
				<router-link
					to="/about"
					class="text-gray-700 hover:text-blue-600 font-medium"
					>About</router-link
				>
				<router-link
					to="/contact"
					class="text-gray-700 hover:text-blue-600 font-medium"
					>Contact</router-link
				>
			</nav>
		</header>

		<main class="container mx-auto p-8">
			<router-view />
		</main>
	</div>
</template>

<script setup>
// This is the only script block needed for this component.
// All component logic for <script setup> goes here.
</script>

<style>
/* A specific style for the active router link is still easiest here */
.router-link-exact-active {
	color: #2563eb; /* A nice blue color */
	font-weight: 600;
}
</style>
