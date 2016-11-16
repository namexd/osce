package com.mx.osce.bean;

public class DrawInfor {
	private int code;

	private String message;

	private DrawBean data;

	public void setCode(int code) {
		this.code = code;
	}

	public int getCode() {
		return this.code;
	}

	public void setMessage(String message) {
		this.message = message;
	}

	public String getMessage() {
		return this.message;
	}

	public void setData(DrawBean data) {
		this.data = data;
	}

	public DrawBean getData() {
		return this.data;
	}
}
