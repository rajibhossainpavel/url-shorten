<template>
	<div class="grid place-items-center h-screen">
		<div class="flex">
			<span class="text-sm border border-2 rounded-l px-4 py-2 bg-gray-300 whitespace-no-wrap">URL</span>
			<input v-model="url" name="field_name" class="border border-2 rounded-r px-4 py-2 w-full" type="text" placeholder="Type URL" />
			<button @click="submit" class="btn">Get Short URL</button>
		</div>
		<div class="text-center">
		<p class="py-6">The Short URL is: {{url_key}}</p>
	</div>
	</div>
	
</template>

<script lang="ts">
import { ref, defineComponent} from 'vue';
import {useShortenUrl} from '@/composables/shorten-url';
export default defineComponent({
	name: "Home",
	setup() {
		const url=ref("");
		const url_key=ref("");
		return {
			url,
			url_key
		}
	},
	methods:{
		submit(){
			async function runAction(payload) {
				const {storeUrl}=useShortenUrl();
				const storeUrlMethod = storeUrl(payload);
				setTimeout(() => {}, 1000);
				await storeUrlMethod;
				const url_key=storeUrlMethod.then(res=>{
					if(res.status==200){
						return res.data.url_key;
					}
				});
				return url_key;
			};
			
			const payload={};
			payload.url=this.url;
			const key=runAction(payload);
			key.then(res=>{
				this.url_key=res.url_key;
				console.log('url_key:', this.url_key);
			});
		}
	}
});
</script>