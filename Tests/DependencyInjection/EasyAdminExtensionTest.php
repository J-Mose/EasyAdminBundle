<?php

namespace JavierEguiluz\Bundle\EasyAdminBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use JavierEguiluz\Bundle\EasyAdminBundle\DependencyInjection\EasyAdminExtension;
use JavierEguiluz\Bundle\EasyAdminBundle\Tests\CommonPhpUnitTestCase;

class EasyAdminExtensionTest extends CommonPhpUnitTestCase
{
    private $container;
    private $loader;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->loader = new EasyAdminExtension();
    }

    /**
     * @dataProvider provideConfigurationFiles
     */
    public function testBackendConfigurations($inputFixtureFilepath, $outputFixtureFilepath)
    {
        if ('2.3.x' === getenv('SYMFONY_VERSION') && !$this->isTestCompatibleWithYamlComponent($inputFixtureFilepath)) {
            $this->markTestSkipped('This test fails because of the behavior of the YAML component in Symfony 2.3.x version.');
        }

        $parsedConfiguration = $this->parseConfigurationFile($inputFixtureFilepath);
        $expectedConfiguration = file_get_contents($outputFixtureFilepath);

        $this->assertEquals($expectedConfiguration, $parsedConfiguration, sprintf('%s configuration is correctly parsed into %s', $inputFixtureFilepath, $outputFixtureFilepath));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The "TestEntity" entity must define its associated Doctrine entity class using the "class" option.
     */
    public function testClassOptionIsMandatoryFoEntityConfiguration()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/class_option_is_mandatory_for_expanded_entity_configuration.yml');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage One of the values of the "fields" option for the "new" view of the "AppBundle\Entity\TestEntity" entity does not define the "property" option.
     */
    public function testPropertyOptionIsMandatoryForFields()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/property_option_is_mandatory_for_fields.yml');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage The values of the "fields" option for the "new" view of the "AppBundle\Entity\TestEntity" entity can only be strings or arrays.
     */
    public function testFieldsCanOnlyBeStringsOrArrays()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/fields_can_only_be_strings_or_arrays.yml');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage When using the expanded configuration format for actions, you must define their "name" option.
     */
    public function testNameOptionIsMandatoryForActionConfiguration()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/name_option_is_mandatory_for_action_configuration.yml');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The name of the "invalid-Action~Name!!" action contains invalid characters (allowed: letters, numbers, underscores).
     */
    public function testActionNameCannotContainInvalidCharacters()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/action_name_cannot_contain_invalid_characters.yml');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The name of the "Invalid action name" action contains invalid characters (allowed: letters, numbers, underscores).
     */
    public function testActionNameCannotContainWhiteSpaces()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/action_name_cannot_contain_white_spaces.yml');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The name of the "7invalidActionName" action contains invalid characters (allowed: letters, numbers, underscores).
     */
    public function testActionNameCannotStartWithANumber()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/action_name_cannot_start_with_a_number.yml');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "easy_admin.design.theme": The theme name can only be "default".
     */
    public function testThemeNameCanOnlyBeDefault()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/theme_name_can_only_be_default.yml');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The value "custom_value" is not allowed for path "easy_admin.design.color_scheme". Permissible values: "dark", "light"
     */
    public function testColorSchemeValuesAreLimited()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/color_scheme_values_are_limited.yml');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Unrecognized option "this_template_name_is_not_valid" under "easy_admin.design.templates"
     */
    public function testOverriddenTemplateNamesAreLimited()
    {
        $this->parseConfigurationFile(__DIR__.'/fixtures/exceptions/overridden_template_names_are_limited.yml');
    }

    /**
     * Tests the template overriding mechanism when a given entity defines
     * its own custom templates in app/Resources/views/easy_admin/<entityName>/<templateName>.html.twig files
     * See EasyAdminExtension::processEntityTemplates()
     */
    public function testEntityOverridesDefaultTemplates()
    {
        $fixturesDir = __DIR__.'/fixtures/templates/overridden_by_entity';

        foreach (range(1, 5) as $i) {
            $parsedConfiguration = $this->parseConfigurationFile($fixturesDir.'/input/admin_00'.$i.'.yml', $fixturesDir);
            $expectedConfiguration = file_get_contents($fixturesDir.'/output/config_00'.$i.'.yml');

            $this->assertEquals($expectedConfiguration, $parsedConfiguration);
        }
    }

    /**
     * Tests the template overriding mechanism when the application defines
     * its own custom templates in app/Resources/views/easy_admin/<templateName>.html.twig files
     * See EasyAdminExtension::processEntityTemplates()
     */
    public function testApplicationOverridesDefaultTemplates()
    {
        $fixturesDir = __DIR__.'/fixtures/templates/overridden_by_application';

        foreach (range(1, 5) as $i) {
            $parsedConfiguration = $this->parseConfigurationFile($fixturesDir.'/input/admin_00'.$i.'.yml', $fixturesDir);
            $expectedConfiguration = file_get_contents($fixturesDir.'/output/config_00'.$i.'.yml');

            $this->assertEquals($expectedConfiguration, $parsedConfiguration);
        }
    }

    public function provideConfigurationFiles($fixturesDir)
    {
        return parent::provideConfigurationFiles(__DIR__.'/fixtures/*');
    }

    /**
     * Given the filepath of the original backend YAML configuration, it returns
     * the configuration parsed by the container and dumped into YAML format.
     *
     * @param string $filepath
     *
     * @return string
     */
    private function parseConfigurationFile($filepath, $kernelRootDir = null)
    {
        $this->container->setParameter('kernel.root_dir', $kernelRootDir ?: __DIR__);

        $inputConfiguration = Yaml::parse(file_get_contents($filepath));
        $this->loader->load($inputConfiguration, $this->container);

        $easyAdminConfigParameter = $this->container->getParameter('easyadmin.config');
        $parsedConfiguration = Yaml::dump(array('easy_admin' => $easyAdminConfigParameter), INF);

        return $parsedConfiguration;
    }

    private function isTestCompatibleWithYamlComponent($filepath)
    {
        $incompatibleTests = array(
            'configurations/input/admin_007.yml',
            'configurations/input/admin_008.yml',
            'configurations/input/admin_013.yml',
            'configurations/input/admin_014.yml',
            'configurations/input/admin_015.yml',
            'configurations/input/admin_020.yml',
            'configurations/input/admin_021.yml',
            'configurations/input/admin_026.yml',
        );

        return !in_array(substr($filepath, -34), $incompatibleTests);
    }
}
