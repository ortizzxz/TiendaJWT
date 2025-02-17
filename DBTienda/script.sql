CREATE DATABASE tienda;
SET NAMES UTF8;
CREATE DATABASE IF NOT EXISTS tienda;
USE tienda;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios( 
id              int(255) auto_increment not null,
nombre          varchar(100) not null,
apellidos       varchar(255),
email           varchar(255) not null,
password        varchar(255) not null,
rol             varchar(20),
CONSTRAINT pk_usuarios PRIMARY KEY(id),
CONSTRAINT uq_email UNIQUE(email)  
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS categorias;
CREATE TABLE IF NOT EXISTS categorias(
id              int(255) auto_increment not null,
nombre          varchar(100) not null,
CONSTRAINT pk_categorias PRIMARY KEY(id) 
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS productos;
CREATE TABLE IF NOT EXISTS productos(
id              int(255) auto_increment not null,
categoria_id    int(255) not null,
nombre          varchar(100) not null,
descripcion     text,
precio          float(100,2) not null,
stock           int(255) not null,
oferta          varchar(2),
fecha           date not null,
imagen          varchar(255),
CONSTRAINT pk_categorias PRIMARY KEY(id),
CONSTRAINT fk_producto_categoria FOREIGN KEY(categoria_id) REFERENCES categorias(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS pedidos;
CREATE TABLE IF NOT EXISTS pedidos(
id              int(255) auto_increment not null,
usuario_id      int(255) not null,
provincia       varchar(100) not null,
localidad       varchar(100) not null,
direccion       varchar(255) not null,
coste           float(200,2) not null,
estado          varchar(20) not null,
fecha           date,
hora            time,
CONSTRAINT pk_pedidos PRIMARY KEY(id),
CONSTRAINT fk_pedido_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS lineas_pedidos;
CREATE TABLE IF NOT EXISTS lineas_pedidos(
id              int(255) auto_increment not null,
pedido_id       int(255) not null,
producto_id     int(255) not null,
unidades        int(255) not null,
CONSTRAINT pk_lineas_pedidos PRIMARY KEY(id),
CONSTRAINT fk_linea_pedido FOREIGN KEY(pedido_id) REFERENCES pedidos(id),
CONSTRAINT fk_linea_producto FOREIGN KEY(producto_id) REFERENCES productos(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- ALTERACION DEL CODIGO DADO
ALTER TABLE lineas_pedidos
DROP FOREIGN KEY fk_linea_producto;

ALTER TABLE lineas_pedidos
ADD CONSTRAINT fk_linea_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE;

-- codigo dummy
-- Inserts para la tabla usuarios
INSERT INTO usuarios (nombre, apellidos, email, password, rol) VALUES
('Juan', 'Pérez', 'juan@email.com', 'password123', 'cliente'),
('María', 'González', 'maria@email.com', 'securepass', 'admin'),
('Carlos', 'Rodríguez', 'carlos@email.com', 'userpass', 'cliente');

-- Inserts para la tabla categorias
INSERT INTO categorias (nombre) VALUES
('Electrónica'),
('Ropa'),
('Hogar'),
('Deportes');

-- Inserts para la tabla productos
INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, oferta, fecha, imagen) VALUES
(1, 'Smartphone XYZ', 'Último modelo de smartphone', 599.99, 50, 0, '2025-01-20', 'smartphone.jpeg'),
(2, 'Camiseta Casual', 'Camiseta de algodón', 19.99, 100, 10, '2025-01-20', 'camiseta.jpg'),
(3, 'Lámpara de Mesa', 'Lámpara moderna para el hogar', 39.99, 30, 0, '2025-01-20', 'lampara.jpg'),
(4, 'Balón de Fútbol', 'Balón oficial de la liga', 29.99, 80, 0, '2025-01-20', 'balon.jpg');

-- Inserts para la tabla pedidos
INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, coste, estado, fecha, hora) VALUES
(1, 'Madrid', 'Madrid', 'Calle Principal 123', 619.98, 'pendiente', '2025-01-20', '10:30:00'),
(2, 'Barcelona', 'Barcelona', 'Avenida Central 456', 59.98, 'enviado', '2025-01-19', '15:45:00');

-- Inserts para la tabla lineas_pedidos
INSERT INTO lineas_pedidos (pedido_id, producto_id, unidades) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 3, 1),
(2, 4, 1);




CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES carts(id),
    FOREIGN KEY (product_id) REFERENCES productos(id)
);


CREATE TABLE blacklisted_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
