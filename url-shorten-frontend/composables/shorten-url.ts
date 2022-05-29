export function useShortenUrl() {
	async function storeUrl(payload){
		const requestOptions = {
			//mode: 'no-cors',
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload)
		};
		let result={};
		try{
			const url="http://localhost:8000/store-url";
			const response= await fetch(url, requestOptions).then(async (res)=>{
				const responseData= await res.json();
				if(responseData.status==200){
					result=responseData.data.original.data;
					return result;
				}
			});
		}catch(e){
			return {'status': 400, 'data': result};
		}
		return {'status': 200, 'data': result};
	};
	return {
		storeUrl
	};
}	