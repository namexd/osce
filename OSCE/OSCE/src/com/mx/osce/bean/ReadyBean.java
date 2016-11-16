package com.mx.osce.bean;

import java.util.List;

public class ReadyBean {
	private int code;

	private String message;

	private List<String> data;

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

	public void setData(List<String> data) {
		this.data = data;
	}

	public List<String> getData() {
		return this.data;
	}
}
