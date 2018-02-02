/** @file    SystemTransform.h
  * @note    HANGZHOU Hikvison Software Co.,Ltd.All Right Reserved.
  * @brief   SystemTransform header file
  *
  *  @version    V2.5.0
  *  @author     heyuanqing
  *  @date       2014/12/05
  *  @note       ������ϸ��Ϣ����ص�
  *
  * @note
  *
  * @warning  Windows 32bit /Linux32 bit version
  */

#ifndef _SYSTEM_TRANSFORM_H_
#define _SYSTEM_TRANSFORM_H_

#ifdef WIN32
#if defined(_WINDLL)
#define SYSTRANS_API  __declspec(dllexport) 
#else 
#define SYSTRANS_API  __declspec(dllimport) 
#endif
#else
#ifndef __stdcall
#define __stdcall
#endif

#ifndef SYSTRANS_API
#define  SYSTRANS_API
#endif
#endif

#define SWITCH_BY_FILESIZE      1       //�ݲ�֧��
#define SWITCH_BY_TIME          2

#define SUBNAME_BY_INDEX        1       //�ݲ�֧��
#define SUBNAME_BY_GLOBALTIME   2

  /************************************************************************
  * ״̬�붨��
  ************************************************************************/
#define SYSTRANS_OK             0x00000000
#define SYSTRANS_E_HANDLE       0x80000000  //ת���������
#define SYSTRANS_E_SUPPORT      0x80000001  //���Ͳ�֧��
#define SYSTRANS_E_RESOURCE     0x80000002  //��Դ������ͷŴ���
#define SYSTRANS_E_PARA         0x80000003  //��������
#define SYSTRANS_E_PRECONDITION 0x80000004  //ǰ������δ���㣬����˳��
#define SYSTRANS_E_OVERFLOW     0x80000005  //�������
#define SYSTRANS_E_STOP         0x80000006  //ֹͣ״̬
#define SYSTRANS_E_FILE         0x80000007  //�ļ�����
#define SYSTRANS_E_MAX_HANDLE   0x80000008  //���·������
#define SYSTRANS_E_MUXER        0x80000010  // �ײ�mp4�����������
#define SYSTRANS_E_OTHER        0x800000ff  //��������

  /************************************************************************
  * ����������Ͷ���
  ************************************************************************/
#define TRANS_SYSHEAD               1   //ϵͳͷ����
#define TRANS_STREAMDATA            2   //��Ƶ�����ݣ�����������������Ƶ�ֿ�����Ƶ�����ݣ�
#define TRANS_AUDIOSTREAMDATA       3   //��Ƶ������
#define TRANS_PRIVTSTREAMDATA       4   //˽����������
#define TRANS_DECODEPARAM           5   //�����������

  /************************************************************************
  * ���ݽṹ����
  ************************************************************************/
  /**	@enum	 SYSTEM_TYPE
   *	@brief   ��װ��ʽ����
   *	@note
   */
typedef enum SYSTEM_TYPE
{
	TRANS_SYSTEM_NULL = 0x0,  // ES
	TRANS_SYSTEM_HIK = 0x1,  //�����ļ��㣬�����ڴ���ʹ洢
	TRANS_SYSTEM_MPEG2_PS = 0x2,  //PS�ļ��㣬��Ҫ���ڴ洢��Ҳ�����ڴ���
	TRANS_SYSTEM_MPEG2_TS = 0x3,  //TS�ļ��㣬��Ҫ���ڴ��䣬Ҳ�����ڴ洢
	TRANS_SYSTEM_RTP = 0x4,  //RTP�ļ��㣬���ڴ���
	TRANS_SYSTEM_MPEG4 = 0x5,  //MPEG4�ļ��㣬���ڴ洢���������÷�ʽ��
	TRANS_SYSTEM_AVI = 0x7,  //AVI�ļ��㣬���ڴ洢
	TRANS_SYSTEM_GB_PS = 0x8,  //����PS����Ҫ���ڹ��곡��
	TRANS_SYSTEM_HLS_TS = 0x9,  //����HLS��TS��װ��������ͨTS��װ
	TRANS_SYSTEM_FLV = 0x0a, //FLV��װ
	TRANS_SYSTEM_RAW = 0x10, //es��ǰ�в�����Ϣ��������(Դ��װ)�����ϵİ汾�����ڲ��Ƽ�ʹ�ã�
	TRANS_SYSTEM_MPEG4_FRONT = 0x0b, //MPEG4�ļ��㣨����ǰ�÷�ʽ��
}SYSTEM_TYPE;

/**	@enum	  DATA_TYPE
 *	@brief    ������������
 *	@note	  ͨ�����ʹ�ø������������ͣ������������Ҫ����Ƶ���ݷֿ�������Ҫ���ò���
 */
typedef enum DATA_TYPE
{
	MULTI_DATA,             //����������
	VIDEO_DATA,             //��Ƶ������
	AUDIO_DATA,             //��Ƶ������
	PRIVATE_DATA,           //˽������
	VIDEO_PARA,             //��Ƶ���������,�����HK_VIDEO_PACK_PARA
	AUDIO_PARA,             //��Ƶ���������,�����HK_AUDIO_PACK_PARA
	PRIVATE_PARA            //˽�����������,�����HK_PRIVATE_PACK_PARA
}DATA_TYPE;

/** @enum   ST_FRAME_TYPE
 *  @brief  ֡����
 *  @note
 */
typedef enum _ST_FRAME_TYPE_
{
	ST_VIDEO_BFRAME = 0,
	ST_VIDEO_PFRAME = 1,
	ST_VIDEO_EFRAME = 2,
	ST_VIDEO_IFRAME = 3,
	ST_AUDIO_FRAME = 4,
	ST_PRIVA_FRAME = 5,
}ST_FRAME_TYPE;

/**	@enum	 ST_PROTOCOL_TYPE
 *	@brief   �ỰЭ������
 *	@note
 */
typedef enum _ST_PROTOCOL_TYPE_
{
	ST_PROTOCOL_RTSP = 1,       //RTSPЭ��
	ST_PROTOCOL_HIK = 2,       //����˽��Э��
	SYSTRANS_PROTOCOL_RTSP = 1,       //��ͬST_PROTOCOL_RTSP,���ݶ��ư汾�Ķ���
	SYSTRANS_PROTOCOL_HIK = 2,       //��ͬST_PROTOCOL_HIK,���ݶ��ư汾�Ķ���
}ST_PROTOCOL_TYPE;

/**	@enum	 ST_SESSION_INFO_TYPE
 *	@brief   �Ự��Ϣ����
 *	@note
 */
typedef enum _ST_SESSION_INFO_TYPE_
{
	ST_SESSION_INFO_SDP = 1,    //SDP��Ϣ
	ST_HIK_HEAD = 2,    //����40�ֽ�ͷ
	SYSTRANS_SESSION_INFO_SDP = 1,    //��ͬST_SESSION_INFO_SDP,���ݶ��ư汾�Ķ���
	SYSTRANS_HIK_HEAD = 2,    //��ͬST_HIK_HEAD,���ݶ��ư汾�Ķ���
}ST_SESSION_INFO_TYPE;

/**	@enum	 ST_ENCRYPT_TYPE
 *	@brief   ��������
 *	@note
 */
typedef enum _ST_ENCRYPT_TYPE_
{
	ST_ENCRYPT_NONE = 0,       //������
	ST_ENCRYPT_AES = 1        //AES ����
}ST_ENCRYPT_TYPE;

/** @enum   ST_MARKBIT
 *  @brief  ���λ����
 *  @note
 */
typedef enum _ST_MARKBIT_TYPE_
{
	ST_UNMARK = 0,    //û�б��
	ST_FRAME_END = 1,    //֡�������(Ŀ���װΪPS��TS��RTP��ESʱ��Ч��
	ST_NEW_FILE = 2,    //���ļ����(Ŀ���װΪMP4��Ч)
}ST_MARKBIT_TYPE;

/**	@struct    SYS_TRANS_PARA
 *	@brief     ת��װ�����ṹ
 *	@note	   ����SYSTRANS_Create�ӿڣ�ֻ֧�ֺ���˽��Э��
 */
typedef struct SYS_TRANS_PARA
{
	unsigned char*  pSrcInfo;       //�����豸����ý����Ϣͷ��Դ������Ϣ��
	unsigned int    dwSrcInfoLen;   //��ǰ�̶�Ϊ40
	SYSTEM_TYPE     enTgtType;      //Ŀ���װ
	unsigned int    dwTgtPackSize;  //����Ϊ0ʱ��ʹ�ÿ���Ĭ��ֵ�����Ŀ��ΪRTP��PS/TS�ȷ�װ��ʽʱ���趨ÿ������С�����ޡ����Ŀ��ΪAVI���趨���֡��
} SYS_TRANS_PARA;

/**	@struct    ST_SESSION_PARA
 *	@brief     ת��װ�Ự��Ϣ����
 *	@note	   ����SYSTRANS_CreateEx������֧��RTSPЭ���SDP��Ϣ
 */
typedef struct _ST_SESSION_PARA_
{
	ST_SESSION_INFO_TYPE  nSessionInfoType;   //�Ự��Ϣ���ͣ�֧�ֺ���ý��ͷ��SDP��Ϣ
	unsigned int          nSessionInfoLen;    //�Ự��Ϣ����
	unsigned char*        pSessionInfoData;   //�Ự��Ϣ����
	SYSTEM_TYPE           eTgtType;           //Ŀ���װ����
	unsigned int          nTgtPackSize;       //���Ŀ��ΪRTP��PS/TS�ȷ�װ��ʽʱ���趨ÿ������С������
} ST_SESSION_PARA, SYS_TRANS_SESSION_PARA;

/**	@struct    AUTO_SWITCH_PARA
 *	@brief     �Զ��л�����
 *	@note
 */
typedef struct AUTO_SWITCH_PARA
{
	unsigned int    dwSwitchFlag;       //SWITCH_BY_TIME��ͨ��ʱ�����л�
	unsigned int    dwSwitchValue;      //ʱ���Է���Ϊ��λ(�趨�Ĺ̶�ʱ��ֵ)
	unsigned int    dwSubNameFlag;      //SUBNAME_BY_GLOBALTIME: �ļ�����ȫ��ʱ������
	char            szMajorName[128];   //��szMajorName = c:\test,�л��ļ�������� = c:\test_������ʱ����.mp4
} AUTO_SWITCH_PARA;

/**	@struct   OUTPUTDATA_INFO
 *	@brief    ������ݶ���
 *	@note
 */
typedef struct OUTPUTDATA_INFO
{
	unsigned char*  pData;              //�ص����ݻ��棬��ָ�����������첽�Ĵ���
	unsigned int    dwDataLen;          //�ص����ݳ���
	unsigned int    dwDataType;         //�������ͣ���TRANS_SYSHEAD,TRANS_STREAMDATA
	unsigned int    dwFlag;             //��������Ƿ�mp4����0:��1����/// mp4����ǰ����������
} OUTPUTDATA_INFO;

/** @struct   DETAIL_DATA_INFO
 *  @brief    ��ϸ������Ϣ
 *  @note
 */
typedef struct _DETAIL_DATA_INFO_
{
	unsigned char*  pData;             //�ص����ݻ��棬��ָ�����������첽�Ĵ���
	unsigned int    nDataLen;          //�ص����ݳ���
	unsigned short  nDataType;         //����������ͣ����궨��
	unsigned short  nFrameType;        //֡���ͣ���ö������
	unsigned int    nTimeStamp;        //ʱ���
	unsigned int    nTimeStampHigh;    //ʱ�����λ,����ʱ����������ֽڵĸ�ʽ
	unsigned short  nMarkbit;          //���(Ŀǰ֧��֡�������½��ļ����ֱ��,�μ�ST_MARKBIT_TYPE��
	unsigned short  nVersion;          //�ṹ��汾��
	unsigned int    reserved[26];      //�����ֶΣ�������չ
										 //reserved[0]��ʾ�ص������Ƿ�mp4������0:��1����/// mp4����ǰ����������
}DETAIL_DATA_INFO;

/**	@struct   HK_SYSTEM_TIME
 *	@brief    ϵͳʱ��
 *	@note
 */
typedef struct _HK_SYSTEM_TIME_
{
	unsigned int           dwYear;          //��
	unsigned int           dwMonth;         //��
	unsigned int           dwDay;           //��
	unsigned int           dwHour;          //Сʱ
	unsigned int           dwMinute;        //��
	unsigned int           dwSecond;        //��
	unsigned int           dwMilliSecond;   //����
	unsigned int           dwReserved;      //����
} HK_SYSTEM_TIME;

/**	@struct   HK_VIDEO_PACK_PARA
 *	@brief    ��Ƶ���������
 *	@note
 */
typedef struct _HK_VIDEO_PACK_PARA_
{
	unsigned int   dwFrameNum;             //֡��
	unsigned int   dwTimeStamp;            //ʱ���
	float          fFrameRate;             //֡��
	unsigned int   dwReserved;             //����
	HK_SYSTEM_TIME stSysTime;              //ȫ��ʱ��    
} HK_VIDEO_PACK_PARA;

/**	@struct    HK_AUDIO_PACK_PARA
 *	@brief     ��Ƶ�������
 *	@note
 */
typedef struct _HK_AUDIO_PACK_PARA_
{
	unsigned int       dwChannels;         //������
	unsigned int	     dwBitsPerSample;    //λ����
	unsigned int       dwSampleRate;       //������
	unsigned int       dwBitRate;          //������
	unsigned int       dwTimeStamp;        //ʱ���
	unsigned int       dwReserved[3];      //����
} HK_AUDIO_PACK_PARA;

/**	@struct    HK_PRIVATE_PACK_PARA
 *	@brief     ˽�����ݴ������
 *	@note
 */
typedef struct _HK_PRIVATE_PACK_PARA_
{
	unsigned int          dwPrivateType;    //˽������
	unsigned int          dwDataType;       //��������
	unsigned int          dwSycVideoFrame;  //ͬ����Ƶ֡
	unsigned int          dwReserved;       //����
	unsigned int          dwTimeStamp;      //ʱ���
	unsigned int          dwReserved1[2];   //����
} HK_PRIVATE_PACK_PARA;


#ifdef __cplusplus
extern "C" {
#endif
	/************************************************************************
	* ��������SYSTRANS_Create
	* ���ܣ�  ͨ��Դ��Ŀ��ķ�װ������������װ��ʽת�����
	* ������  phTrans	        - ���صľ��
	*		 pstTransInfo       - ת����Ϣ����ָ��
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_Create(void** phTrans, SYS_TRANS_PARA* pstTransInfo);

	/************************************************************************
	* ��������SYSTRANS_Start
	* ���ܣ�  ��ʼ��װ��ʽת��
	* ������  hTrans	            - ת�����
	*        szSrcPath          - Դ�ļ�·���������NULL������Ϊ��
	*        szTgtPath          - Ŀ���ļ�·���������NULL������Ϊ��
	* ˵����  Դ�ļ�ΪTS��֧���ļ�ģʽ�������ⲿ���ļ�������ģʽ����
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_Start(void* hTrans, const char* szSrcPath, const char* szTgtPath);

	/************************************************************************
	* ��������SYSTRANS_AutoSwitch
	* ���ܣ�  Ŀ��Ϊ�ļ�ʱ���Զ��л��洢�ļ�
	* ������  hTrans	        - ת�����
	*		  pstPara       - �Զ��л��ļ��Ĳ����ṹָ��
	* ˵����  ��֧������һ�Σ����ö�η��ز�֧��
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_AutoSwitch(void* hTrans, AUTO_SWITCH_PARA* pstPara);

	/************************************************************************
	* ��������SYSTRANS_ManualSwitch
	* ���ܣ�  Ŀ��Ϊ�ļ�ʱ���ֶ��л��洢�ļ�
	* ������  hTrans	        - ת�����
	*		 szTgtPath      - ��һ�洢�ļ���·��
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_ManualSwitch(void* hTrans, const char* szTgtPath);

	/************************************************************************
	* ��������SYSTRANS_InputData
	* ���ܣ�  ԴΪ��ģʽ����������
	* ������  hTrans	- ת�����
	*		  pData		        - Դ��������ָ��
	*		  dwDataLen         - �����ݴ�С
	*		  enType	        - �������ͣ���δʹ�ã�ͳһ��MULTI_DATA
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_InputData(void* hTrans, DATA_TYPE enType, unsigned char* pData, unsigned int dwDataLen);

	/************************************************************************
	* ��������SYSTRANS_GetTransPercent
	* ���ܣ�  ת�ļ�ģʽʱ�����ת���ٷֱȣ���ʱֻ֧��Դ��HIK��PS��MPEG4
	* ������  hTrans	        - ת�����
	*		 pdwPercent     - ת���ٷֱ�
	* ����ֵ��״̬��
	************************************************************************/

	SYSTRANS_API int __stdcall SYSTRANS_GetTransPercent(void* hTrans, unsigned int* pdwPercent);

	/************************************************************************
	* ��������SYSTRANS_RegisterOutputDataCallBack
	* ���ܣ�  Ŀ��Ϊ��ģʽ��ע��ת�������ݻص�
	* ������  hTrans				    - ת�����
	*		 OutputDataCallBack     - ����ָ��
	*		 dwUser				    - �û�����
	* ����ֵ��״̬��
	* ˵����  3GPP��֧�ֻص�
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_RegisterOutputDataCallBack(void* hTrans, void(__stdcall * pfnOutputDataCallBack)(OUTPUTDATA_INFO* pDataInfo, void* pUser), void* pUser);


	/************************************************************************
	* ��������SYSTRANS_RegisterOutputDataCallBackEx
	* ���ܣ�  Ŀ��Ϊ��ģʽ��ע��ת�������ݻص�
	* ������  hTrans				    - ת�����
	*		 OutputDataCallBack	    - ����ָ��
	*		 dwUser				    - �û�����
	* ����ֵ��״̬��
	* ˵����  �ýӿ���SYSTRANS_RegisterOutputDataCallBack����һ�£�Ϊ����
	*           V2.3.0.6֮ǰ�İ汾�������˽ӿڣ�����ʹ��SYSTRANS_RegisterOutputDataCallBack
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_RegisterOutputDataCallBackEx(void* hTrans, void(__stdcall * OutputDataCallBack)(OUTPUTDATA_INFO* pDataInfo, void* pUser), void* pUser);


	/************************************************************************
	* ��������SYSTRANS_RegisterDetailDataCallBack
	* ���ܣ�  Ŀ��Ϊ��ģʽ��ע��ת�������ݻص�
	* ������  hTrans             - ת�����
	*        pfDetailCbf        - ����ָ��
	*        dwUser             - �û�����
	* ����ֵ��״̬��
	* ˵����  ������ݵ���ϸ��Ϣ,֧��Ŀ���װΪPS/TS/RTP/MP4/FLV/ES
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_RegisterDetailDataCallBack(void* hTrans, void(__stdcall * pfDetailCbf)(DETAIL_DATA_INFO* pDataInfo, void* pUser), void* pUser);

	/************************************************************************
	* ��������SYSTRANS_Stop
	* ���ܣ�  ֹͣת��
	* ������  hTrans	 - ת�����
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_Stop(void* hTrans);

	/************************************************************************
	* ��������SYSTRANS_Release
	* ���ܣ�  �ͷ�ת�����
	* ������  hTrans	        - ת�����
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_Release(void* hTrans);

	/************************************************************************
	* ��������SYSTRANS_CreateEx
	* ���ܣ�  SDP��Ϣ����ת��װ����
	* ������  hTrans             - ת�����
	* ������  eType              - Э������
	* ������  pstInfo            - ת��װ�Ự����
	* ����ֵ��״̬��
	* ˵����  �ýӿ���SYSTRANS_Create����չ�����Ӷ�ʹ��SDP��Ϣ���������֧��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_CreateEx(void** phTrans, ST_PROTOCOL_TYPE eType, ST_SESSION_PARA* pstInfo);

	/************************************************************************
	* ��������SYSTRANS_SetGlobalTime
	* ���ܣ�  ����ȫ��ʱ��
	* ������  hTrans	            - ת�����
	* ������  pstGlobalTime	    - ȫ��ʱ��
	* ����ֵ��״̬��
	* ˵����  Դ�������к���˽�������ӣ���Я��ȫ��ʱ�䣬����Դ������ȫ��ʱ��Ϊ׼�����û���Ч
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_SetGlobalTime(void* hTrans, HK_SYSTEM_TIME* pstGlobalTime);

	/************************************************************************
	* ��������SYSTRANS_SetEncryptKey
	* ���ܣ�  ������Կ
	* ������  hTrans             - ת�����
	* ������  eType              - ��������
	* ������  pKey               - ��Կ������
	* ������  nKeyLen            - ��Կ���ȣ���λΪBit
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_SetEncryptKey(void* hTrans, ST_ENCRYPT_TYPE eType, char* pKey, unsigned int nKeyLen);

	/************************************************************************
	* ��������SYSTRANS_GetVersion
	* ���ܣ�  ��ȡ�汾��
	* ������  ��
	* ����ֵ���汾��
	************************************************************************/
	SYSTRANS_API int __stdcall SYSTRANS_GetVersion();

	/************************************************************************
	* ��������SYSTRANS_OpenStreamAdvanced
	* ���ܣ�  SDP��Ϣ����ת��װ����,���ڼ���֮ǰ���ư汾
	* ������  hTrans	      - ת�����
	* ������  nProtocolType	  - Э������
	* ������  pstSessionInfo  - SDP��Ϣ
	* ������  pstTransInfo	  - ת����Ϣ����ָ��
	* ����ֵ��״̬��
	************************************************************************/
	SYSTRANS_API long __stdcall SYSTRANS_OpenStreamAdvanced(void** phTrans, int nProtocolType, SYS_TRANS_SESSION_PARA* pstTransSessionInfo);

#ifdef __cplusplus
}
#endif

#endif //_SYSTEM_TRANSFORM_H_
