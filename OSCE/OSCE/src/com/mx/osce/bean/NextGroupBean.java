package com.mx.osce.bean;

import java.util.ArrayList;
import java.util.List;

/** 下一組考生 */
public class NextGroupBean {

	private int code;

	private String message;

	private List<Data> data;

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

	public void setData(List<Data> data) {

		this.data = data;

	}

	public List<Data> getData() {
		return this.data;
	}

	public class Data {
		private String student_id;

		private String student_name;

		private String student_code;

		private String station_id;

		private String room_id;

		public String getRoom_id() {
			return room_id;
		}

		public void setRoom_id(String room_id) {
			this.room_id = room_id;
		}

		public String getStation_id() {
			return station_id;
		}

		public void setStation_id(String station_id) {
			this.station_id = station_id;
		}

		public void setStudent_id(String student_id) {
			this.student_id = student_id;
		}

		public String getStudent_id() {
			return this.student_id;
		}

		public void setStudent_name(String student_name) {
			this.student_name = student_name;
		}

		public String getStudent_name() {
			return this.student_name;
		}

		public void setStudent_code(String student_code) {
			this.student_code = student_code;
		}

		public String getStudent_code() {
			return this.student_code;
		}

	}

}
