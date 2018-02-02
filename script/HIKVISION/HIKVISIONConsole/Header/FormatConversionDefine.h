/** @file       FormatConversionDefine.h
 *  @note       HangZhou Hikvision Digital Technology Co., Ltd. All Right Reserved.
 *  @brief      Definitions of struct/enum/variables/constant/error-code used by APIs of Media Format Conversion dynamic library
 *
 *  @author     Media Play SDK Team of Hikvision
 *
 *  @version    V4.1.0
 *  @date       2014/03/03
 *
 *  @warning
 */
#ifndef _FC_DEFINE_H_
#define _FC_DEFINE_H_

 /**
  *  @brief  Definitions in Windows OS
  */
  //#ifdef _WINDOWS
#ifdef _WIN32
#ifdef FORMATCONVERSION_EXPORTS
#define FC_API __declspec(dllexport)
#else
#define FC_API __declspec(dllimport)
#endif
#endif/*_WINDOWS*/

  /**
   *  @brief  Definitions in linux OS
   *  @note   linux platform will use XWindow (x11)
   */
#ifdef __linux__
#define FC_API
#define __stdcall
#endif/*__linux__*/

   /**
	*  @brief  Definitions in MAX OS
	*  @note   apple we will use NSView , here type it as void *
	*/
#ifdef __APPLE__
#define FC_API
#define __stdcall
#endif/*__APPLE__*/

	/* �����ʽת��������� */
typedef void* FCHANDLE;

/* ״̬�붨�� */
#define FC_OK               0           ///< �ɹ����޴���
#define FC_E_HANDLE         0x80000000  ///< �������Ч�ľ��
#define FC_E_SUPPORT        0x80000001  ///< ��֧�ֵĹ���
#define FC_E_BUFOVER        0x80000002  ///< ��������
#define FC_E_CALLORDER      0x80000003  ///< ��������˳�����
#define FC_E_PARAMETER      0x80000004  ///< ����Ĳ���
#define FC_E_NEEDMOREDATA   0x80000005  ///< ��Ҫ���������
#define FC_E_RESOURCE       0x80000006  ///< ��Դ����ʧ��
#define FC_E_STREAM         0x80000007  ///< ��������
#define FC_E_DEMUXER        0x80000008  ///< demuxer�ڵ��쳣����
#define FC_E_MUXER          0x80000009  ///< muxer�ڵ��쳣����
#define FC_E_DECODER        0x8000000a  ///< decoderģ�����
#define FC_E_ENCODER        0x8000000b  ///< encoderģ�����
#define FC_E_POSTPROC       0x8000000c  ///< ����
#define FC_E_UNKNOW         0x800000ff  ///< δ֪�Ĵ���

/* ������װ��ʽ */
typedef enum FC_FormatType
{
	/* format types ��HKMI���屣��һ�� */
	FC_FORMAT_NULL = 0x0,      ///< �޷�װ
	FC_FORMAT_HIK = 0x0001,   ///< ����˽�з�װ
	FC_FORMAT_MPEG2_PS = 0x0002,   ///< PS��װ
	FC_FORMAT_MPEG2_TS = 0x0003,   ///< TS��װ
	FC_FORMAT_RTP = 0x0004,   ///< RTP��װ

	/* ���·�װ��ʽ���������ݲ�֧�� */
	FC_FORMAT_MP4 = 0x0005,   ///< MP4��װ
	FC_FORMAT_ASF = 0x0006,   ///< ASF��װ
	FC_FORMAT_AVI = 0x0007,   ///< AVI��װ

	/* ���½�ת��ⶨ�� */
	FC_FORMAT_MOV = 0x0020,
	FC_FORMAT_3GP = 0x0021,
	FC_FORMAT_MKV = 0x0022,
	FC_FORMAT_WEBM = 0x0023,
	FC_FORMAT_FLV = 0x0024,
	FC_FORMAT_SWF,
	FC_FORMAT_RM,
};

/* ����Ƶ�����ʽ */
typedef enum FC_CodecType
{
	FC_CODEC_NONE = 0x0,      ///< �ޱ���

	/* video codecs ��HKMI���屣��һ�¡�[0x0800����Ϊ����������Ƶ����] */
	FC_CODEC_V_HIK264 = 0x0001,   ///< ��������
	FC_CODEC_V_MPEG2 = 0x0002,   ///< MPEG2
	FC_CODEC_V_MPEG4 = 0x0003,   ///< MPEG4
	FC_CODEC_V_MJPEG = 0x0004,   ///< MJPEG
	FC_CODEC_V_H265 = 0x0005,   ///< ��׼H265
	FC_CODEC_V_H264 = 0x0100,   ///< ��׼H264

	/* ������Ƶ���룬���������ݲ�֧�� */
	FC_CODEC_V_YV12 = 0x0801,
	FC_CODEC_V_H263,
	FC_CODEC_V_FFH264,
	FC_CODEC_V_MSMPEG4V1 = 0x0811,
	FC_CODEC_V_MSMPEG4V2 = 0x0812,
	FC_CODEC_V_MSMPEG4V3 = 0x0813,
	FC_CODEC_V_WMV1 = 0x0821,
	FC_CODEC_V_WMV2 = 0x0822,
	FC_CODEC_V_FLV1,
	FC_CODEC_V_FLASHSV,
	FC_CODEC_V_VP8,
	FC_CODEC_V_MXPEG,

	/* audio codecs ��HKMI���屣��һ�¡� [0x8000����Ϊ����������Ƶ����]*/
	FC_CODEC_A_MP2 = 0x2000,  ///< MP2
	FC_CODEC_A_AAC = 0x2001,  ///< AAC
	FC_CODEC_A_PCMU = 0x7110,  ///< G711U
	FC_CODEC_A_PCMA = 0x7111,  ///< G711A
	FC_CODEC_A_G722 = 0x7221,  ///< G722
	FC_CODEC_A_G723_1 = 0x7231,  ///< G7231
	FC_CODEC_A_G726 = 0x7262,  ///< G726
	FC_CODEC_A_G729 = 0x7290,  ///< G729

	/* ������Ƶ���룬���������ݲ�֧�� */
	FC_CODEC_A_PCM = 0x8001,
	FC_CODEC_A_MP3,
	FC_CODEC_A_AMR_NB,
	FC_CODEC_A_AMR_WB,
	FC_CODEC_A_AC3,
	FC_CODEC_A_DTS,
	FC_CODEC_A_VORBIS,
	FC_CODEC_A_DVAUDIO,
	FC_CODEC_A_WMAV1 = 0x8010,
	FC_CODEC_A_WMAV2 = 0x8011,
	FC_CODEC_A_WMAVOICE,
	FC_CODEC_A_WMAPRO,
	FC_CODEC_A_WMALOSSLESS,
	FC_CODEC_A_FLAC,
};

//�������������
typedef enum FC_DataType
{
	FC_MULTI_DATA = 0x0,      ///< �����
	FC_VIDEO_DATA,                      ///< ��Ƶ��
	FC_AUDIO_DATA,                      ///< ��Ƶ��
	FC_PRIVATE_DATA,                    ///< ˽����
	FC_VIDEO_PARA,                      ///< ��Ƶ����
	FC_AUDIO_PARA,                      ///< ��Ƶ����
	FC_PRIVATE_PARA,                    ///< ˽�в���
};

#define FC_MAX_TRACK_COUNT      8       ///< �������

/* ��Ƶ��Ϣ�ṹ�� */
typedef struct FC_VIDEO_INFO_STRU
{
	FC_CodecType        enCodec;        ///< ��Ƶ����

	/*
	 * ���enCodec = FC_CODEC_NONE������Ƶ��������Ӧ�������²���������Ч
	 */
	unsigned int        nTrackId;       ///< ����� [��δʹ��]
	unsigned int        nBitRate;       ///< �ر���������
	float               fFrameRate;     ///< �ر�������Ƶ֡��
	unsigned short      nWidth;         ///< ���ز�����������ͼ����[16����]
	unsigned short      nHeight;        ///< ���ز�����������ͼ��߶�[16����]

}FC_VIDEO_INFO;

/* ��Ƶ��Ϣ�ṹ�� */
typedef struct FC_AUDIO_INFO_STRU
{
	FC_CodecType        enCodec;        ///< ��Ƶ����

	/*
	 * ���enCodec = FC_CODEC_NONE������Ƶ��������Ӧ�������²���������Ч
	 */
	unsigned int        nTrackId;       ///< ����� [��δʹ��]
	unsigned short      nChannels;      ///< ������
	unsigned short      nBitsPerSample; ///< ��λ��
	unsigned int        nSamplesRate;   ///< ������
	unsigned int        nBitRate;       ///< ������

}FC_AUDIO_INFO;

/* ˽����Ϣ�ṹ�� */
typedef struct FC_PRIVT_INFO_STRU
{
	unsigned int        nType;          ///< ˽����������
	unsigned int        nTrackId;       ///< ����� [��δʹ��]
}FC_PRIVT_INFO;

/** @struct MEDIA_INFO
 *  @brief  ý����Ϣ
 */
typedef struct FC_MEDIA_INFO_STRU
{
	FC_FormatType       enSystemFormat;     ///< ��װ��ʽ

	/* ����Ƶ����Ҫ�����ʱ����1������������Ƶ������Ƶ��������0 */
	unsigned int        nVideoStreamCount;  ///< ��Ƶ�����������ֵ��FC_MAX_TRACK_COUNT
	unsigned int        nAudioStreamCount;  ///< ��Ƶ�����������ֵ��FC_MAX_TRACK_COUNT
	unsigned int        nPrivtStreamCount;  ///< ˽�������������ֵ��FC_MAX_TRACK_COUNT

	/* ���²���ѡ������Ҫ����Ӧ�����Զ���0������ת��װ*/

	/* ��Ƶ��Ϣ */
	FC_VIDEO_INFO       stVideoInfo[FC_MAX_TRACK_COUNT];

	/* ��Ƶ��Ϣ */
	FC_AUDIO_INFO       stAudioInfo[FC_MAX_TRACK_COUNT];

	/* ˽����Ϣ */
	FC_PRIVT_INFO       stPrivtInfo[FC_MAX_TRACK_COUNT];

	unsigned int        nReserved[4];       ///< �����ֶ�

} FC_MEDIA_INFO;


/* ����Э������ */
#define FC_PROTOCOL_NULL        0       ///< ������Э��
#define FC_PROTOCOL_HIK         1       ///< ����˽��Э��
#define FC_PROTOCOL_RTSP        2       ///< RTSPЭ�� [��δʵ��]

/* ������Ϣ���� */
#define FC_SESSION_MEDIADATA    0       ///< δ֪����Э���δ֪������Ϣ���ͣ�����ֱ��ʹ��ý�����ݽ��н�������������100k����������
#define FC_SESSION_HIK          1       ///< 40�ֽڵĺ���˽�н�����Ϣ/����ý����Ϣͷ/��������ͷ
#define FC_SESSION_SDP          2       ///< SDP,rfc4566���� [��δʵ��]

/* ��������� */
#define FC_UNKNOW_PACKET        0       ///< δ֪����
#define FC_VIDEO_PACKET         1       ///< ��Ƶ��
#define FC_AUDIO_PACKET         2       ///< ��Ƶ��
#define FC_PRIVT_PACKET         3       ///< ˽�а�
#define FC_HIK_FILE_HEADER      4       ///< �����ļ�ͷ

/** @struct FC_SESSION_INFO
 *  @brief  ������Ϣ�ṹ��
 */
typedef struct FC_SESSION_INFO_STRU
{
	unsigned int        nSessionInfoType;   ///< ������Ϣ���ͣ�FC_SESSION_MEDIADATA / FC_SESSION_HIK / FC_SESSION_SDP
	unsigned int        nSessionInfoLen;    ///< ������Ϣ����
	unsigned char       *pSessionInfoData;  ///< ������Ϣ����
	unsigned int        nReserved[4];       ///< �����ֶ�
} FC_SESSION_INFO;

#endif //_FC_DEFINE_H_
