package com.mx.bluetooth.bean;

import java.util.List;

/** 当前考生小组信息实体类 */
public class CurrentGroupBean {

	private int code;

	private String message;

	private List<CurrentGroupStudentBean> data;

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

	public void setData(List<CurrentGroupStudentBean> data) {
		this.data = data;
	}

	public List<CurrentGroupStudentBean> getData() {
		return this.data;
	}

}
