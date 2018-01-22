package com.mx.osce.bean;

import java.util.ArrayList;

public class StudentStatuBean {
	private int code;
	private String message;
	private Statu data;

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

	public Statu getData() {
		return data;
	}

	public void setData(Statu data) {
		this.data = data;
	}

	public class Statu {
		private String start_time;

		private String student_id;

		public void setStart_time(String start_time) {
			this.start_time = start_time;
		}

		public String getStart_time() {
			return this.start_time;
		}

		public void setStudent_id(String student_id) {
			this.student_id = student_id;
		}

		public String getStudent_id() {
			return this.student_id;
		}
	}

}