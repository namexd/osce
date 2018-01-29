<script>
   	$(function () {
   		var msg = {!! json_encode($errors->all()) !!};
   		if (msg) {
   			for (var name in msg) {
				if (msg[name][0]==1) {
					uselayer2(31,msg[name].substring(1,msg[name].length));
				} else {
					uselayer2(3,msg[name]);
				}
   			}	   				
   		}   			
	});
</script>
