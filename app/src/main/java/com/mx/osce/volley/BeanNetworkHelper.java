package com.mx.osce.volley;

import org.json.JSONException;
import org.json.JSONObject;

import com.android.volley.VolleyError;
import com.mx.osce.bean.BaseInfo;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.bean.NextGroupBean;

import android.content.Context;
import android.util.Log;

public class BeanNetworkHelper extends NetworkHelper<BaseInfo> {

	public BeanNetworkHelper(Context context) {
		super(context);

	}

	@Override
	protected void disposeVolleyError(VolleyError error) {
		
	}

	@Override
	protected void disposeResponse(JSONObject response) {

		try {
			String code = response.getString("code");
			String message = response.getString("message");
			Log.i("BeanNetWoekHelper JsonResponse", response.toString());

		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}

}
