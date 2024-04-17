<?php
namespace App\Repository\IRepository;

interface IUnitOfWork {
    public function category(): ICategoryRepository;
    public function discount(): IDiscountRepository;
    public function product(): IProductRepository;
    public function role(): IRoleRepository;
    public function company(): ICompanyRepository;
    public function cart(): ICartRepository;
    public function user(): IUserRepository;
    public function order(): IOrderRepository;
    public function order_detail(): IOrderDetailRepository;
    public function payment(): IPaymentRepository;
    public function inventory(): IInventoryRepository;
    public function product_color(): IProductColorRepository;
    public function product_size(): IProductSizeRepository;
}
