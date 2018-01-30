package com.mx.osce.audio;

public class AudioData {
	float time; // 备忘录音时间长度
	String filePath; // 备忘录音文件路径
	String mCurrentTime; // 备忘录音时的时间

	public AudioData(float time, String filePath, String currentTime) {
		super();
		this.time = time;
		this.filePath = filePath;
		this.mCurrentTime = currentTime;
	}

	public float getTime() {
		return time;
	}

	public void setTime(float time) {
		this.time = time;
	}

	public String getFilePath() {
		return filePath;
	}

	public void setFilePath(String filePath) {
		this.filePath = filePath;
	}

	public String getmCurrentTime() {
		return mCurrentTime;
	}

	public void setmCurrentTime(String mCurrentTime) {
		this.mCurrentTime = mCurrentTime;
	}

}
