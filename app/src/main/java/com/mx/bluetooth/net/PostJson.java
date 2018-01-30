/*package com.mx.bluetooth.net;

import java.io.BufferedInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Map;

import com.google.gson.Gson;

import android.util.Log;

public class PostJson {
	public Map<String, String> execute(NotificationDto dto) {  
	    InputStream inputStream = null;  
	    HttpURLConnection urlConnection = null;  
	    try {  
	        URL url = new URL(getUrl);  
	        urlConnection = (HttpURLConnection) url.openConnection();  
	  
	         optional request header   
	        urlConnection.setRequestProperty("Content-Type", "application/json; charset=UTF-8");  
	  
	         optional request header   
	        urlConnection.setRequestProperty("Accept", "application/json");  
	        dto.setCreator(java.net.URLEncoder.encode(dto.getCreator(), "utf-8"));  
	          
	        // read response  
	         for Get request   
	        urlConnection.setRequestMethod("POST");  
	        urlConnection.setDoOutput(true);  
	        DataOutputStream wr = new DataOutputStream(urlConnection.getOutputStream());  
	        Gson gson = new Gson();  
	        String jsonString = gson.toJson(dto);  
	        wr.writeBytes(jsonString);  
	        wr.flush();  
	        wr.close();  
	        // try to get response  
	        int statusCode = urlConnection.getResponseCode();  
	        if (statusCode == 200) {  
	            inputStream = new BufferedInputStream(urlConnection.getInputStream());  
	            String response = HttpUtils.convertInputStreamToString(inputStream);  
	            Map<String, String> resultMap = gson.fromJson(response, Map.class);  
	            if (resultMap != null && resultMap.size() > 0) {  
	                Log.i("applyDesigner", "please check the map with key");  
	            }  
	            return resultMap;  
	        }  
	    }  
	    catch(Exception e)  
	    {  
	        e.printStackTrace();  
	    }  
	    finally  
	    {  
	        if (inputStream != null) {  
	            try {  
	                inputStream.close();  
	            } catch (IOException e) {  
	                e.printStackTrace();  
	            }  
	        }  
	        if (urlConnection != null) {  
	            urlConnection.disconnect();  
	        }  
	    }  
	    return null;  
	}  
}
*/