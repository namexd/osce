package com.mx.osce.bean;

import java.util.List;

public class GradePointBeanInfor {
	private int code;

	private String message;

	private List<GradePointBean_Net> data;

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

	public List<GradePointBean_Net> getData() {
		return data;
	}

	public void setData(List<GradePointBean_Net> data) {
		this.data = data;
	}
}
