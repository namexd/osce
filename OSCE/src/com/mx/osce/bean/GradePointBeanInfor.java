package com.mx.osce.bean;

import java.util.List;

public class GradePointBeanInfor {
	private int code;

	private String message;

	private List<GradePointBean> data;

	public int getCode() {
		return code;
	}

	public void setCode(int code) {
		this.code = code;
	}

	public String getMessage() {
		return message;
	}

	public void setMessage(String message) {
		this.message = message;
	}

	public List<GradePointBean> getData() {
		return data;
	}

	public void setData(List<GradePointBean> data) {
		this.data = data;
	}
}
