package com.mx.osce.json;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.mx.osce.bean.CurrentGroupStudentBean;

public class StudentDataJson {
	public static ArrayList<CurrentGroupStudentBean> studentJson(String data) {
		ArrayList<CurrentGroupStudentBean> studentList = null;
		try {
			JSONObject object = new JSONObject(data);
			if ("success".equals(object.getString("message"))) {
				JSONArray dataArr = object.getJSONArray("data");
				studentList = new ArrayList<CurrentGroupStudentBean>();
				for (int i = 0; i < dataArr.length(); i++) {
					JSONObject studentObj = dataArr.getJSONObject(i);
					CurrentGroupStudentBean bean = new CurrentGroupStudentBean();
					bean.setStudent_name(studentObj.getString("student_name"));
					bean.setStudent_avator(studentObj.getString("student_avator"));
					bean.setStudent_code(studentObj.getString("student_code"));
					bean.setStudent_name(studentObj.getString("student_name"));
					studentList.add(bean);
					bean = null;
				}
				return studentList;
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
		return null;
	}
}
