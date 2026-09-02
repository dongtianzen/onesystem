<?php

namespace Drupal\Tests\dashpage\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * 测试 Dashpage 模块的三个路由是否正常访问.
 *
 * @group dashpage
 */
class DashpageBasicTest extends BrowserTestBase {

  /**
   * 用最简单的主题跑测试，加快速度、避免主题层面的干扰.
   */
  protected $defaultTheme = 'stark';

  /**
   * 依赖的模块.
   */
  protected static $modules = ['dashpage', 'node', 'taxonomy'];

  /**
   * 测试 /index-page 路由（最简单的一个，没有参数）.
   *
   * 匿名用户默认拥有 "access content" 权限，应该能直接 200 访问.
   */
  public function testIndexPageLoads() {
    $this->drupalGet('/index-page');
    $this->assertSession()->statusCodeEquals(200);
    // 断言页面标题按路由定义应该是 "Oneband".
    $this->assertSession()->titleEquals('Oneband | Drupal');
  }

  /**
   * 测试 /dashboard/category/{name} 路由.
   *
   * 因为不知道数据库里实际有哪些分类名，先测试传入一个明显不存在的值，
   * 确认 accessCheck 修复后不会报 500 致命错误（哪怕是 404 也比 500 好，
   * 500 说明代码逻辑本身有问题）.
   */
  public function testDashboardCategoryDoesNotFatal() {
    $this->drupalGet('/dashboard/category/nonexistent-test-category');
    // 不应该是 500（内部服务器错误），可以是 200（正常处理了不存在的情况）
    // 或 404（明确说明找不到），但绝不能是 500.
    $status = $this->getSession()->getStatusCode();
    $this->assertNotEquals(500, $status, '页面不应返回 500 致命错误');
  }

  /**
   * 测试 /newspage/term/{vid_name}/{term_tid}/{second_tid} 路由.
   *
   * second_tid 有默认值 "all"，所以理论上可以只传前两个参数测试，
   * 但路由本身要求三段都要有，这里显式传入.
   */
  public function testNewspageTermDoesNotFatal() {
    $this->drupalGet('/newspage/term/tags/1/all');
    $status = $this->getSession()->getStatusCode();
    $this->assertNotEquals(500, $status, '页面不应返回 500 致命错误');
  }

}
