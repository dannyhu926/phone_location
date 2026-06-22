
CREATE TABLE dbo.phone_location (
    id INT NOT NULL , --  IDENTITY(1,1),  -- 不要自增，因为insert时省略了字段名称，无法开启标识插入。
    pref NVARCHAR(10) NULL DEFAULT NULL,
    phone NVARCHAR(20) NOT NULL,
    province NVARCHAR(45) NULL DEFAULT NULL,
    city NVARCHAR(45) NULL DEFAULT NULL,
    isp NVARCHAR(45) NULL DEFAULT NULL,
    isp_type SMALLINT NOT NULL DEFAULT 0,
    post_code NVARCHAR(100) NULL DEFAULT NULL,
    city_code NVARCHAR(10) NOT NULL,
    area_code NVARCHAR(100) NULL DEFAULT NULL,
    create_time DATETIME NOT NULL DEFAULT GETDATE(), 

    -- 主键约束
    CONSTRAINT PK_phone_location_id PRIMARY KEY CLUSTERED (id),
    -- 手机号唯一索引
    CONSTRAINT UK_phone_location_phone UNIQUE NONCLUSTERED (phone),
    -- 普通索引
    INDEX IX_phone_location_city_code (city_code)
);
GO

-- 添加字段注释（SQL Server专用扩展存储过程）
EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'号段前缀',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = pref;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'手机号',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = phone;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'省份',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = province;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'城市',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = city;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'运营商类型名称',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = isp;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'运营商类型 1：移动 2：联通 3：电信 4：广电 5：工信',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = isp_type;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'邮编',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = post_code;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'区号',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = city_code;

EXEC sp_addextendedproperty
    @name = N'MS_Description', @value = N'行政区划编码',
    @level0type = N'SCHEMA', @level0name = dbo,
    @level1type = N'TABLE',  @level1name = phone_location,
    @level2type = N'COLUMN', @level2name = area_code;
GO