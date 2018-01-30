package com.mx.bluetooth.bean;

public class LoginBean {
	private int code;
	private String Message;
	private LoginResultBean data;

	public int getCode() {
		return code;
	}

	public void setCode(int code) {
		this.code = code;
	}

	public String getMessage() {
		return Message;
	}

	public void setMessage(String message) {
		Message = message;
	}

	public LoginResultBean getResultBean() {
		return data;
	}

	public void setResultBean(LoginResultBean data) {
		this.data = data;
	}
}
