<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* server/export/index.twig */
class __TwigTemplate_a491ba9692973b1ba784c6afecfe18567208ef0a113d4dfa2e80139b14b3311e extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'selection_options' => [$this, 'block_selection_options'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "export.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 25
        ob_start(function () { return ''; });
        // line 26
        echo "  ";
echo _gettext("@SERVER@ will become the server name.");
        $context["filename_hint"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        $this->parent = $this->loadTemplate("export.twig", "server/export/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
echo _gettext("Exporting databases from the current server");
    }

    // line 5
    public function block_selection_options($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 6
        echo "  <div class=\"card mb-3\" id=\"databases_and_tables\">
    <div class=\"card-header\">";
echo _gettext("Databases");
        // line 7
        echo "</div>
    <div class=\"card-body\">
      <div class=\"mb-3\">
        <button type=\"button\" class=\"btn btn-secondary\" id=\"db_select_all\">";
echo _gettext("Select all");
        // line 10
        echo "</button>
        <button type=\"button\" class=\"btn btn-secondary\" id=\"db_unselect_all\">";
echo _gettext("Unselect all");
        // line 11
        echo "</button>
      </div>

      <select class=\"form-select\" name=\"db_select[]\" id=\"db_select\" size=\"10\" multiple aria-label=\"";
echo _gettext("Databases");
        // line 14
        echo "\">
        ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["databases"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["database"]) {
            // line 16
            echo "          <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 16), "html", null, true);
            echo "\"";
            echo ((twig_get_attribute($this->env, $this->source, $context["database"], "is_selected", [], "any", false, false, false, 16)) ? (" selected") : (""));
            echo ">
            ";
            // line 17
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["database"], "name", [], "any", false, false, false, 17), "html", null, true);
            echo "
          </option>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['database'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 20
        echo "      </select>
    </div>
  </div>
";
    }

    public function getTemplateName()
    {
        return "server/export/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  108 => 20,  99 => 17,  92 => 16,  88 => 15,  85 => 14,  79 => 11,  75 => 10,  69 => 7,  65 => 6,  61 => 5,  54 => 3,  49 => 1,  45 => 26,  43 => 25,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "server/export/index.twig", "/home/tweetsp/public_html/talk/phpMyAdmin/templates/server/export/index.twig");
    }
}
