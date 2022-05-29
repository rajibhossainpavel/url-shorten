<template>
	<div class="grid place-items-center h-screen">
		<div class="flex">
			<span class="text-sm border border-2 rounded-l px-4 py-2 bg-gray-300 whitespace-no-wrap">URL</span>
			<input v-model="url" name="field_name" class="border border-2 rounded-r px-4 py-2 w-full" type="text" placeholder="Type URL" />
			<button @click="submit" class="btn">Get Short URL</button>
		</div>
		<div class="text-center">
		<p class="py-6">The Short URL is: {{short_url}}</p>
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
		const short_url=ref("");
		const submit=()=>{
			async function runAction(payload) {
				const {storeUrl}=useShortenUrl();
				const storeUrlMethod = storeUrl(payload);
				setTimeout(() => {}, 1000);
				await storeUrlMethod;
				const short_url=storeUrlMethod.then(res=>{
					if(res.status==200){
						return res.data.short_url;
					}
				});
				//console.log('short_url: ', short_url);
				return short_url;
			};
			
			const payload={};
			payload.url=url.value;
			const returned_short_url=runAction(payload);
			
			returned_short_url.then(res=>{
			console.log('returned_short_url: ', res);
				short_url.value=res;
				console.log('short_url:', short_url);
			});
		}
		return {
			url,
			short_url,
			submit
		}
	}
});
</script>