<script setup>
window.console.log('App Started ...');
</script>

<template>
	<div id="indagodigital--app">
		<header>
			<nav>
				<router-link to="/">Home</router-link> |
				<router-link to="/services">Services</router-link> |
				<router-link to="/portfolio">Portfolio</router-link> |
				<router-link to="/about">About</router-link> |
				<router-link to="/contact">Contact</router-link>
			</nav>
		</header>

		<main>
			<!-- This is where the component for the current route will be rendered -->
			<router-view />
		</main>
	</div>
</template>

<style scoped></style>
