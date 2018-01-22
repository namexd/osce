package com.mx.osce.custom;


import com.mx.osce.R;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewConfiguration;

/**
 * 
 * 
 * @author wuwenjie
 * 
 */
public class CalendarCard extends View {

	private static final int TOTAL_COL = 4; // 7
	private  int TOTAL_ROW = 6; // 6
	private  int TOTAL_SCORE = 1;

	private Paint mCirclePaint;
	private Paint mCirclePaint_click;// 
	private Paint mTextPaint; // 
	private int mViewWidth; // 
	private int mViewHeight; // 
	private int mCellSpace; // 
	private Row rows[] = new Row[100]; // 
	private OnCellClickListener mCellClickListener; // 
	private int touchSlop; //
	private boolean callBackCellSpace;
	private boolean ClickCell=false;
	
	private int clickX=0; 
	private int clickY=0;
	private int Postion=-1;
	
	
	private Cell mClickCell;
	private float mDownX;
	private float mDownY;

	/**
	 * ӿ
	 * 
	 * @author wuwenjie
	 * 
	 */
	public interface OnCellClickListener {
		void clickDate(int d,float clickX,float clickY); // 

		void changeDate(CustomDate date); //
	}

	public CalendarCard(Context context, AttributeSet attrs, int defStyleAttr) {
		super(context, attrs, defStyleAttr);
		init(context);
	}

	public CalendarCard(Context context, AttributeSet attrs) {
		super(context, attrs);
		init(context);
	}

	public CalendarCard(Context context) {
		super(context);
		init(context);
	}

	public CalendarCard(Context context, OnCellClickListener listener) {
		super(context);
		this.mCellClickListener = listener;
		init(context);
	}
	public void setON(OnCellClickListener listener) {
		this.mCellClickListener = listener;
	}
	@Override
	protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
		int h=TOTAL_ROW*110;
		int w=TOTAL_COL*120;
		setMeasuredDimension(w,h);
	}
	protected void onLayout(boolean changed, int l, int t, int r, int b) {
		super.onLayout(changed, l, t, r, b);
	}

	private void init(Context context) {
		mTextPaint = new Paint(Paint.ANTI_ALIAS_FLAG);
		mCirclePaint = new Paint(Paint.ANTI_ALIAS_FLAG);
		mCirclePaint.setStyle(Paint.Style.STROKE);
		mCirclePaint.setColor(getResources().getColor(R.color.color_card_storke)); // E0E0E0
		touchSlop = ViewConfiguration.get(context).getScaledTouchSlop();
		initDate();
	}

	private void initDate() {
		fillDate();//
	}

	private void fillDate() {
		int loopcount=TOTAL_COL;
		int day = -1;
		for (int j = 0; j < TOTAL_ROW; j++) {
			rows[j] = new Row(j);
			if(j==TOTAL_ROW-1&&(TOTAL_SCORE%TOTAL_COL!=0||TOTAL_SCORE<TOTAL_COL)){
				loopcount=TOTAL_SCORE%TOTAL_COL;
			}
			for (int i = 0; i < loopcount; i++) {
				day++;
				rows[j].cells[i] = new Cell(day, State.CURRENT_MONTH_DAY, i, j);
				if (Postion!=-1&& day == Postion ) {
					rows[j].cells[i] = new Cell(day, State.TODAY, i, j);
					mClickCell=rows[j].cells[i];
				}
			}
		}
	}

	@Override
	protected void onDraw(Canvas canvas) {
		super.onDraw(canvas);
		System.out.println("onDraw");
		mCirclePaint_click = new Paint(Paint.ANTI_ALIAS_FLAG);
		mCirclePaint_click.setStyle(Paint.Style.FILL);
		mCirclePaint_click.setColor(getResources().getColor(R.color.color_card_seleted));
		for (int i = 0; i < TOTAL_ROW; i++) {
			if (rows[i] != null) {
				rows[i].drawCells(canvas);
			}
		}
		if(clickX!=0&&clickY!=0){
			mTextPaint.setColor(Color.parseColor("#fffffe"));
//			canvas.drawCircle((float) (mCellSpace * (clickX + 0.5)),
//					(float) ((clickY + 0.5) * mCellSpace), mCellSpace / 3+10,
//					mCirclePaint_click);
			canvas.drawRect((float) (mCellSpace * (clickX + 0.5))-mCellSpace / 3, (float) ((clickY + 0.5) * mCellSpace)-mCellSpace / 3, (float) (mCellSpace * (clickX + 0.5))+mCellSpace / 3, (float) ((clickY + 0.5) * mCellSpace)+mCellSpace / 3, mCirclePaint_click);
			int date = rows[clickY].cells[clickX].number;
			String content =date + "";
//			canvas.drawText(content,
//					(float) ((clickX + 0.5) * mCellSpace - mTextPaint
//							.measureText(content) / 2), (float) ((clickY + 0.7)
//							* mCellSpace - mTextPaint
//							.measureText(content, 0, 1) / 2), mTextPaint);
			if(date>=7){
				canvas.drawText(content,
						(float) ((clickX + 0.4) * mCellSpace - mTextPaint
						.measureText(content) / 3), (float) ((clickY + 0.6)
								* mCellSpace ), mTextPaint);
			}else{
			canvas.drawText(content,
					(float) ((clickX + 0.4) * mCellSpace ), (float) ((clickY + 0.6)
							* mCellSpace ), mTextPaint);
			}
		}else if(ClickCell){
			mTextPaint.setColor(Color.parseColor("#fffffe"));
//			canvas.drawCircle((float) (mCellSpace * (clickX + 0.5)),
//					(float) ((clickY + 0.5) * mCellSpace), mCellSpace / 3+10,
//					mCirclePaint_click);
			canvas.drawRect((float) (mCellSpace * (clickX + 0.5))-mCellSpace / 3, (float) ((clickY + 0.5) * mCellSpace)-mCellSpace / 3, (float) (mCellSpace * (clickX + 0.5))+mCellSpace / 3, (float) ((clickY + 0.5) * mCellSpace)+mCellSpace / 3, mCirclePaint_click);
			int date = rows[clickY].cells[clickX].number;
			String content = date + "";
//			canvas.drawText(content,
//					(float) ((clickX + 0.5) * mCellSpace - mTextPaint
//							.measureText(content) / 2), (float) ((clickY + 0.7)
//							* mCellSpace - mTextPaint
//							.measureText(content, 0, 1) / 2), mTextPaint);
			if(date>=7){
				canvas.drawText(content,
						(float) ((clickX + 0.4) * mCellSpace - mTextPaint
						.measureText(content) / 3), (float) ((clickY + 0.6)
								* mCellSpace ), mTextPaint);
			}else{
			canvas.drawText(content,
					(float) ((clickX + 0.4) * mCellSpace ), (float) ((clickY + 0.6)
							* mCellSpace ), mTextPaint);
			}
		}
		
	}

	@Override
	protected void onSizeChanged(int w, int h, int oldw, int oldh) {
		super.onSizeChanged(w, h, oldw, oldh);
		mViewWidth = w;
		mViewHeight = h;
		mCellSpace = Math.min(mViewHeight / TOTAL_ROW, mViewWidth / TOTAL_COL);
		if (!callBackCellSpace) {
			callBackCellSpace = true;
		}
		mTextPaint.setTextSize(mCellSpace / 3);
	}

	@Override
	public boolean onTouchEvent(MotionEvent event) {
		switch (event.getAction()) {
		case MotionEvent.ACTION_DOWN:
			mDownX = event.getX();
			mDownY = event.getY();
			System.out.println("£");
			break;
		case MotionEvent.ACTION_UP:
			float disX = event.getX() - mDownX;
			float disY = event.getY() - mDownY;
			if (Math.abs(disX) < touchSlop && Math.abs(disY) < touchSlop) {
				int col = (int) (mDownX / mCellSpace);
				int row = (int) (mDownY / mCellSpace);
				if((TOTAL_SCORE%TOTAL_COL!=0)&&row==TOTAL_ROW-1&&col>=(TOTAL_SCORE%TOTAL_COL)){
					break;	
				}
				measureClickCell(col, row);
			}
			
			break;
		default:
			break;
		}

		return true;
	}

	/**
	 * ĵ
	 * @param col
	 * @param row
	 */
	private void measureClickCell(int col, int row) {
		if (col >= TOTAL_COL || row >= TOTAL_ROW)
			return;
//		mClickCell.state=State.CURRENT_MONTH_DAY;
			if (rows[row] != null) {
				mClickCell = new Cell(rows[row].cells[col].number,
						State.TODAY, rows[row].cells[col].i,
						rows[row].cells[col].j);
	            
				int date = rows[row].cells[col].number;
				clickX=col;
				clickY=row;
				this.Postion=date;
				mCellClickListener.clickDate(date,(float) (mCellSpace * (clickX + 0.5)),(float) (mCellSpace * (clickY + 0.5)));
				ClickCell=true;
		}
		update();
	}

	/**
	 * 
	 * 
	 * @author wuwenjie
	 * 
	 */
	class Row {
		public int j;

		Row(int j) {
			this.j = j;
		}

		public Cell[] cells = new Cell[TOTAL_COL];

		// Ƶ
		public void drawCells(Canvas canvas) {
			for (int i = 0; i < cells.length; i++) {
				if (cells[i] != null) {
					cells[i].drawSelf(canvas);
				}
			}
		}

	}

	/**
	 * 
	 * 
	 * @author wuwenjie
	 * 
	 */
	class Cell {
		public int number;
		public State state;
		public int i;
		public int j;

		public Cell(int number, State state, int i, int j) {
			super();
			this.number = number;
			this.state = state;
			this.i = i;
			this.j = j;
		}

		public void drawSelf(Canvas canvas) {
			switch (state) {
			case TODAY: // 
				mTextPaint.setColor(Color.parseColor("#fffffe"));
//				canvas.drawCircle((float) (mCellSpace * (i + 0.5)),
//						(float) ((j + 0.5) * mCellSpace), mCellSpace / 3+10,
//						mCirclePaint_click);
				canvas.drawRect((float) (mCellSpace * (i + 0.5))-mCellSpace / 3, (float) ((j + 0.5) * mCellSpace)-mCellSpace / 3, (float) (mCellSpace * (i+ 0.5))+mCellSpace / 3, (float) ((j + 0.5) * mCellSpace)+mCellSpace / 3, mCirclePaint_click);
				
				
				System.out.println(""+i+j);
				break;
			case CURRENT_MONTH_DAY: // ǰ
				mTextPaint.setColor(Color.BLACK);
//				canvas.drawCircle((float) (mCellSpace * (i + 0.5)),
//						(float) ((j + 0.5) * mCellSpace), mCellSpace / 3+10,
//						mCirclePaint);
				canvas.drawRect((float) (mCellSpace * (i + 0.5))-mCellSpace / 3, (float) ((j + 0.5) * mCellSpace)-mCellSpace / 3, (float) (mCellSpace * (i + 0.5))+mCellSpace / 3, (float) ((j + 0.5) * mCellSpace)+mCellSpace / 3, mCirclePaint);
				break;
			
			default:
				break;
			}
			// 
			String content = number + "";
//			canvas.drawText(content,
//					(float) ((i + 0.5) * mCellSpace - mTextPaint
//							.measureText(content) / 2), (float) ((j + 0.7)
//							* mCellSpace - mTextPaint
//							.measureText(content, 0, 1) / 2), mTextPaint);
			if(number>=10){
				canvas.drawText(content,
						(float) ((i + 0.4) * mCellSpace - mTextPaint
						.measureText(content) / 3), (float) ((j + 0.6)
								* mCellSpace ), mTextPaint);
			}else{
			canvas.drawText(content,
					(float) ((i + 0.4) * mCellSpace ), (float) ((j + 0.6)
							* mCellSpace ), mTextPaint);
			}
		}
	}

	/**
	 * 
	 * @author wuwenjie 
	 */
	enum State {
		TODAY,CURRENT_MONTH_DAY;
	}
	public void update() {
		fillDate();
		invalidate();
	}
	public void setPostion(int Postion){
	this.Postion=Postion;
	update();
	}
	public void totlescore(int totlescore){
		totlescore+=1;
		TOTAL_SCORE=totlescore;
		TOTAL_ROW = totlescore/TOTAL_COL;
		if(totlescore%TOTAL_COL!=0)
			TOTAL_ROW+=1;
		update();
		}

}