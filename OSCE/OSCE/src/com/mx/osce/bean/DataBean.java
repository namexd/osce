package com.mx.osce.bean;

public class DataBean {
	/** 音频 */
	public static final int AUDIO = 1;
	/** 相机 */
	public static final int CAMERA = 2;
	/** 描点 */
	public static final int TIMEPOINT = 3;

	private int mTag;

	private long timePoint;

	float time; // 记录数据时间长度

	String filePath; // 文件路径

	String mCurrentTime; // 记录数据时间

	public DataBean(int mTag) {
		super();
		this.mTag = mTag;
	}

	public int getmTag() {
		return mTag;
	}

	public long getTimePoint() {
		return timePoint;
	}

	public void setTimePoint(long timePoint) {
		this.timePoint = timePoint;
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

	public void setmTag(int mTag) {
		this.mTag = mTag;
	}

}
