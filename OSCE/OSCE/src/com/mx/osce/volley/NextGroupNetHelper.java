package com.mx.osce.volley;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.android.volley.VolleyError;
import com.google.gson.Gson;
import com.mx.osce.bean.NextGroupBean;

import android.content.Context;
import android.util.Log;

public class NextGroupNetHelper extends NetworkHelper<ArrayList<NextGroupBean>> {

	public NextGroupNetHelper(Context context) {
		super(context);
		// TODO Auto-generated constructor stub
	}

	@Override
	protected void disposeVolleyError(VolleyError error) {
		// TODO Auto-generated method stub

	}

	@Override
	protected void disposeResponse(JSONObject response) {
		try {
			JSONArray jsonArray = response.getJSONArray("data");
			Gson gon = new Gson();
			NextGroupBean nextGroup = gon.fromJson(response.toString(), NextGroupBean.class);
			Log.i("NextGroupNetHelper---dispose", nextGroup.getData().get(0).getStudent_name());

		} catch (JSONException e) {
			e.printStackTrace();
		}

	}

}
