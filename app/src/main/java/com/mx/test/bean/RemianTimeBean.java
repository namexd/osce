package com.mx.test.bean;

public class RemianTimeBean {
	private int code;

	private String message;

	private Time data;

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

	public Time getTime() {
		return data;
	}

	public void setTime(Time time) {
		this.data = time;
	}

	public class Time {
		private int remainTime;

		public void setRemainTime(int remainTime) {
			this.remainTime = remainTime;
		}

		public int getRemainTime() {
			return this.remainTime;
		}
	}
}
