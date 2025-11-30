"""
Script untuk generate flowchart Admin ke format PNG
Menggunakan library graphviz

Install terlebih dahulu:
pip install graphviz

Atau di Windows:
1. Download Graphviz dari https://graphviz.org/download/
2. Install dan tambahkan ke PATH
3. pip install graphviz
"""

from graphviz import Digraph

def create_admin_flowchart():
    # Create new directed graph
    dot = Digraph(comment='Admin Flowchart', format='png')
    dot.attr(rankdir='TB', size='12,16')
    dot.attr('node', shape='box', style='rounded,filled', fontname='Arial')
    
    # Start
    dot.node('start', 'ADMIN LOGIN', shape='ellipse', fillcolor='lightgreen')
    
    # Dashboard
    dot.node('dashboard', 'Dashboard Admin', fillcolor='lightblue')
    
    # Main Menu
    dot.node('menu', 'Pilih Menu', shape='diamond', fillcolor='lightyellow')
    
    # Menu Options
    dot.node('user_mgmt', 'Manajemen User', fillcolor='plum')
    dot.node('pipeline_mgmt', 'Manajemen Pipeline', fillcolor='plum')
    dot.node('nasabah_mgmt', 'Manajemen Nasabah', fillcolor='plum')
    dot.node('import_data', 'Import Data', fillcolor='plum')
    dot.node('report', 'Laporan & Rekap', fillcolor='plum')
    dot.node('logout', 'LOGOUT', shape='ellipse', fillcolor='lightpink')
    
    # Edges
    dot.edge('start', 'dashboard')
    dot.edge('dashboard', 'menu')
    dot.edge('menu', 'user_mgmt', label='1')
    dot.edge('menu', 'pipeline_mgmt', label='2')
    dot.edge('menu', 'nasabah_mgmt', label='3')
    dot.edge('menu', 'import_data', label='4')
    dot.edge('menu', 'report', label='5')
    dot.edge('menu', 'logout', label='6')
    
    # Save
    dot.render('admin-flowchart-basic', directory='.', cleanup=True)
    print("✅ Flowchart berhasil dibuat: admin-flowchart-basic.png")

if __name__ == '__main__':
    create_admin_flowchart()
