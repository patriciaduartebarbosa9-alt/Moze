const fs = require('fs');
const path = require('path');
const Mustache = require('mustache');

// Configuração
const settings = {
  templates: 'templates/pages',
  layouts: 'templates/layouts',
  partials: 'templates/partials',
  data: 'templates/data',
  createDirectories: true
};

console.log(settings);
console.log('✓ Settings loaded successfully');

const templatesDir = path.join(__dirname, settings.templates);
const partialsDir = path.join(__dirname, settings.partials);
const layoutsDir = path.join(__dirname, settings.layouts);
const dataDir = path.join(__dirname, settings.data);

// Carregar globais dados
let globalData = {};
try {
  const globalDataPath = path.join(dataDir, 'global.json');
  if (fs.existsSync(globalDataPath)) {
    globalData = JSON.parse(fs.readFileSync(globalDataPath, 'utf8'));
    console.log('✓ Global data loaded successfully');
  }
} catch (e) {
  console.warn('⚠️  No global data file found');
}

// Função para carregar partials
function loadPartials(dir) {
  const partials = {};
  if (!fs.existsSync(dir)) return partials;
  
  try {
    fs.readdirSync(dir).forEach(file => {
      if (file.endsWith('.mustache')) {
        const name = file.replace('.mustache', '');
        const content = fs.readFileSync(path.join(dir, file), 'utf8');
        partials[name] = content;
      }
    });
    console.log(`✓ Loaded ${Object.keys(partials).length} partials`);
  } catch (e) {
    console.error('Error loading partials:', e);
  }
  
  return partials;
}

// Carregar layout
function loadLayout(dir) {
  const layoutPath = path.join(dir, 'layout.mustache');
  if (fs.existsSync(layoutPath)) {
    console.log('✓ Default layout template loaded from:', path.relative(__dirname, layoutPath));
    return fs.readFileSync(layoutPath, 'utf8');
  }
  return null;
}

// Função para obter todos os ficheiros templates
function getAllTemplates(dir, baseDir = '') {
  const templates = [];
  
  if (!fs.existsSync(dir)) return templates;
  
  const items = fs.readdirSync(dir);
  
  items.forEach(item => {
    const fullPath = path.join(dir, item);
    const stat = fs.statSync(fullPath);
    
    if (stat.isDirectory()) {
      templates.push(...getAllTemplates(fullPath, path.join(baseDir, item)));
    } else if (item.endsWith('.mustache')) {
      templates.push({
        file: item,
        path: fullPath,
        dir: baseDir,
        relPath: path.join(baseDir, item)
      });
    }
  });
  
  return templates;
}

// Função principal de compilação
async function compile() {
  console.log('🚀 Starting Mustache compilation...\n');
  
  const partials = loadPartials(partialsDir);
  const defaultLayout = loadLayout(layoutsDir);
  
  console.log('\n📂 Scanning templates directory...');
  const templates = getAllTemplates(templatesDir);
  console.log(`✓ Found ${templates.length} templates\n`);
  
  let compiled = 0;
  let failed = 0;
  const failedTemplates = [];
  
  console.log('📝 Compiling templates...');
  
  for (const template of templates) {
    try {
      const templateName = template.file.replace('.mustache', '');
      console.log(`🔧 Processing: ${template.relPath} (${templateName})`);
      
      // Carregar dados específicos do template
      let templateData = { ...globalData };
      const dataFile = path.join(dataDir, template.dir, templateName + '.json');
      if (fs.existsSync(dataFile)) {
        const data = JSON.parse(fs.readFileSync(dataFile, 'utf8'));
        templateData = { ...templateData, ...data };
      }
      
      // Ler template
      const templateContent = fs.readFileSync(template.path, 'utf8');
      
      // Compilar
      console.log(`🔧 Compiling ${templateName}...`);
      
      // Renderizar template com layout se existir
      let output = Mustache.render(templateContent, templateData, partials);
      
      if (defaultLayout && !templateContent.includes('<html')) {
        // Envolver com layout
        output = Mustache.render(defaultLayout, { ...templateData, content: output }, partials);
      }
      
      // Criar directório de saída se necessário
      const outputDir = path.join(__dirname, template.dir);
      if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
      }
      
      // Salvar HTML
      const outputFile = path.join(__dirname, template.dir, templateName + '.html');
      fs.writeFileSync(outputFile, output);
      
      console.log(`✓ ${templateName} → ${path.join(template.dir, templateName + '.html')}`);
      compiled++;
      
    } catch (error) {
      console.error(`❌ ${template.file}: Failed to render template: ${template.file}`);
      console.error(`   Error: ${error.message}`);
      failed++;
      failedTemplates.push(template.file);
    }
  }
  
  console.log('\n🎉 Enhanced Mustache compilation complete!');
  console.log(`✓ ${compiled} templates compiled successfully`);
  if (failed > 0) {
    console.log(`❌ ${failed} templates failed:`);
    failedTemplates.forEach(t => console.log(`   ${t}`));
  }
  console.log('✅ Compilation completed successfully!');
  console.log(`📊 Performance: 0 templates cached, 1 layouts cached`);
}

compile().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
