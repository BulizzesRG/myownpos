<template>
	<section class="h-screen">
		<div class="px-6 h-full text-gray-800">
			<div
				class="flex xl:justify-center lg:justify-between justify-center items-center flex-wrap h-full g-6"
			>
				<div
					class="grow-0 shrink-1 md:shrink-0 basis-auto xl:w-6/12 lg:w-6/12 md:w-9/12 mb-12 md:mb-0"
				>
					<img
						src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
						class="w-full"
						alt="Sample image"
					>
				</div>
				<div class="xl:ml-20 xl:w-5/12 lg:w-5/12 md:w-8/12 mb-12 md:mb-0">
					<form method="post" @submit.prevent="login">
						<div class="mb-6">
							<div
								class="text-center"
							>
								<p class="text-end font-semibold mb-5">
									Inicio de sesión
								</p>
							</div>
							<label for="email">Correo eléctronico</label>
							<input
								id="email"
								type="email"
								class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
								placeholder="Correo eléctronico"
								@keypress="validationEmailMessage = ''"
								v-model="email"
								required
							>
							<span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1" v-show="validationEmailMessage">
								{{ validationEmailMessage }}
							</span>
						</div>
						<div class="mb-6">
							<label for="password">Contraseña</label>
							<input
								id="password"
								type="password"
								class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
								placeholder="Contraseña"
								@keypress="validationPasswordMessage = ''"
								v-model="password"
								required
							>
							<span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1" v-show="validationPasswordMessage">
								{{ validationPasswordMessage }}
							</span>							
						</div>

						<div class="flex justify-between items-center mb-6">
							<a href="#!" class="text-gray-800">¿Olvidáste tu contraseña?</a>
						</div>

						<div class="text-center lg:text-left">
							<button
								type="submit"
								class="inline-block px-7 py-3 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
							>
								Enviar
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
export default {
	layout: 'basic',
	name: 'LoginPage',
	data() {
		return {
			email: '',
			password: '',
			validationEmailMessage: '',
			validationPasswordMessage: ''
		}
	},
	methods: {
		async login () {
			try {
				await this.$auth.loginWith('laravelSanctum', {
					data: {
						email: this.email,
						password: this.password
					}
				});
				this.$router.push('/');
			} catch (error) {
				const data = error.response.data;
				if(error.response.status == 422){
					this.validationError = true;
					this.validationEmailMessage = data.errors.hasOwnProperty('email') ? data.errors.email[0] : '';
					this.validationPasswordMessage = data.errors.hasOwnProperty('password') ? data.errors.password[0] : '';
					this.password = '';
				}
				console.log(error);
				console.log(error.response);
				console.log(error.response.status);
			}
		}
	}
}
</script>
