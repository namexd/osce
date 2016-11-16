package com.mx.osce.bean;

import java.util.List;

public class VideoBean {
	private int code;
	private int id;
	private String name;
	private String ip;
	private int status;
	private int port;
	private String channel;
	private String username;
	private String password;
	private int realport;
	public int getRealport() {
		return realport;
	}
	public void setRealport(int realport) {
		this.realport = realport;
	}
	private List<VideoBean> data;
	
	
	public List<VideoBean> getData() {
		return data;
	}
	public void setData(List<VideoBean> data) {
		this.data = data;
	}
	public int getCode() {
		return code;
	}
	public void setCode(int code) {
		this.code = code;
	}
	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getIp() {
		return ip;
	}
	public void setIp(String ip) {
		this.ip = ip;
	}
	public int getStatus() {
		return status;
	}
	public void setStatus(int status) {
		this.status = status;
	}
	public int getPort() {
		return port;
	}
	public void setPort(int port) {
		this.port = port;
	}
	public String getChannel() {
		return channel;
	}
	public void setChannel(String channel) {
		this.channel = channel;
	}
	public String getUsername() {
		return username;
	}
	public void setUsername(String username) {
		this.username = username;
	}
	public String getPassword() {
		return password;
	}
	public void setPassword(String password) {
		this.password = password;
	}
	
	
	

	
}
